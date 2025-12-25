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
    $query = $this->query();

    if (!empty($filters['note_id'])) {
      $query->where('note_id', $filters['note_id']);
    }

    if (!empty($filters['file_type'])) {
      $query->where('file_type', 'like', '%' . $filters['file_type'] . '%');
    }

    return $query->latest()->get();
  }
}
