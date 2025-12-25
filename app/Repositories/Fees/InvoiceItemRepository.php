<?php

namespace App\Repositories\Fees;

use App\Models\InvoiceItem;
use App\Repositories\BaseRepository;

class InvoiceItemRepository extends BaseRepository
{
    public function __construct(InvoiceItem $model)
    {
        parent::__construct($model);
    }
}
