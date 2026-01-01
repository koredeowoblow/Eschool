<?php

namespace App\Repositories\ClassRooms;

use App\Models\Attachment;
use App\Repositories\BaseRepository;

class AttachmentRepository extends BaseRepository
{
  public function __construct(Attachment $model)
  {
    parent::__construct($model);
  }

  /**
   * List attachments with filters.
   */
  public function list(array $filters = []): \Illuminate\Database\Eloquent\Collection
  {
    $query = $this->query()->with(['note', 'classRoom', 'subject']);

    if (!empty($filters['note_id'])) {
      $query->where('note_id', $filters['note_id']);
    }

    if (!empty($filters['class_id'])) {
      $query->where('class_id', $filters['class_id']);
    }

    if (!empty($filters['subject_id'])) {
      $query->where('subject_id', $filters['subject_id']);
    }

    if (!empty($filters['file_type'])) {
      $query->where('file_type', 'like', '%' . $filters['file_type'] . '%');
    }

    return $query->latest()->get();
  }
}
