<?php

namespace App\Services\Erp;

enum QuotationResult
{
    /** The order has a quotation in the ERP (now or from an earlier send). */
    case Sent;

    /** The send failed; the reason is in the order's `erp_error`. */
    case Failed;

    /** Another send of the same order is running; nothing was done. */
    case Busy;
}
