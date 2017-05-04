<?php

namespace App\Services;

use Auth;
use App\Models\Comment;
use InvalidArgumentException;
use App\Repositories\CommentRepository;
use App\Repositories\SubmissionRepository;

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
	 * @param string $hashid The hashid of the submission.
	 * @param string $contents The comment contents.
	 * @return Comment
	 */
	public function postCommentOnSubmission($hashid, $contents)
	{
		$submission = $this->submissionRepository->getByHashId($hashid);
		if ( ! $submission) {
			throw new InvalidArgumentException('Comment posted on nonexistent submission hash ' . $hashid);
		}

		if ( ! strlen($contents)) {
		    throw new InvalidArgumentException('Empty comment posted on submission id ' . $submission->id);
        }

		$comment = $submission->comments()->create([
			'user_id' => Auth::user()->id,
			'parent_id' => null,
			'contents' => $contents,
		]);

		// Ensure the comment has all of its attributes by grabbing it fresh from the database.
		return $this->commentRepository->getByKey($comment->id);
	}
}
