<?php

namespace App\Services;

use Auth;
use RuntimeException;
use App\Models\Comment;
use App\Models\CommentVote;
use App\Jobs\CompileComment;
use InvalidArgumentException;
use App\Jobs\CompileSubmission;
use App\Repositories\CommentRepository;
use App\Jobs\Notifications\CommentReply;
use App\Repositories\SubmissionRepository;
use App\Jobs\Notifications\CommentOnSubmission;

class CommentManager
{
	/**
	 * @var CommentRepository
	 */
	protected $commentRepository;

	/**
	 * @var SubmissionRepository
	 */
	protected $submissionRepository;

	public function __construct(
		CommentRepository $commentRepository,
		SubmissionRepository $submissionRepository
	)
	{
		$this->commentRepository = $commentRepository;
		$this->submissionRepository = $submissionRepository;
	}

	/**
	 * Post a comment - whether it's in response to a submission, or in response to another
	 * comment.
	 *
	 * @param string $contents  The comment contents.
	 * @param string $hashid    The hashid of the submission.
	 * @param string $parent_id The parent_id
	 * @return Comment|string   The successfully posted comment, or an error.
	 */
	public function postComment($contents, $hashid, $parent_id = null)
	{
		$submission = $this->submissionRepository->getByHashId($hashid);
		if ( ! $submission) {
			throw new InvalidArgumentException('Comment posted on nonexistent submission hash ' . $hashid);
		}

		if ( ! strlen($contents)) {
			throw new InvalidArgumentException('Empty comment posted on submission id ' . $submission->id);
		}

		// This is not an exceptional occurence - can happen if replying to a user that deleted their
		// comment.
		if ($parent_id && ! Comment::where('id', $parent_id)->count()) {
			return 'The comment you are replying to has been deleted.';
		}

		$originalContents = $contents;
		$contents = stripUnsafeTags($contents);

		$scriptKiddie = (strpos($originalContents, '<script') !== false);
		if ( ! strlen($contents) || $scriptKiddie) {
			issue('Potential hacker posted a comment.', [
				'user_id' => Auth::user()->id,
				'contents' => $originalContents,
			]);

			if ($scriptKiddie) {
				$contents = '<p>This is unrelated to the video, but I really enjoy sucking cock.</p>';
			}
		}

		$comment = $submission->comments()->create([
			'user_id' => Auth::user()->id,
			'parent_id' => $parent_id,
			'contents' => $contents,
		]);

		// Automatically updoot our own comment.
		$comment->votes()->create([
			'user_id' => Auth::user()->id,
			'up' => 1,
		]);

		if ($parent_id) {
			$parentComment = $this->commentRepository->getByKey($parent_id);

			if ( ! $parentComment) {
				throw new RuntimeException('Comment id ' . $comment->id . ' missing parent id ' . $parent_id);
			}

			// Ensure the parent's reply count gets re-compiled.
			dispatch(new CompileComment($parentComment));

			// Notify the parent's user of this reply.
			dispatch(new CommentReply($parentComment));
		}
		else {
			dispatch(new CommentOnSubmission($submission));
		}

		// Ensure the submission's comment count gets re-compiled.
		dispatch(new CompileSubmission($submission));

		// Ensure the comment has all of its attributes by grabbing it fresh from the database.
		$comment = $this->commentRepository->getByKey($comment->id);
		$comment->load('user');
		$comment->setUserUp();
		return $comment;
	}

	/**
	 * Vote on a comment.
	 *
	 * @param string $hashid The hashid of the comment being voted on.
	 * @param int    $value
	 * @return string|true
	 */
	public function vote($hashid, $value)
	{
		$value = (int) $value;

		if ( ! in_array($value, [-1, 1])) {
			throw new InvalidArgumentException('Comment vote not -1 or 1.');
		}

		// Get the comment being voted on.
		$comment = $this->commentRepository->getByHashId($hashid);
		if ( ! $comment) {
			return 'The comment you are voting on has been deleted.';
		}

		// Get the vote if it exists.
		$commentVote = CommentVote::where('comment_id', $comment->id)
			->where('user_id', Auth::user()->id)
			->first();

		// Create it otherwise.
		if ( ! $commentVote) {
			$commentVote = new CommentVote;
		}

		// Update / insert it.
		$commentVote->comment_id = $comment->id;
		$commentVote->user_id = Auth::user()->id;
		$commentVote->up = ($value == 1) ? 1 : 0;
		$commentVote->down = ($value == -1) ? 1 : 0;
		$commentVote->save();

		// Ensure the comment's vote numbers get recompiled.
		dispatch(new CompileComment($comment));

		return true;
	}
}
