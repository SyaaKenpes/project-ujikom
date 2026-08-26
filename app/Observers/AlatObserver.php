<?php

namespace App\Observers;

use App\Models\Alat;

class AlatObserver
{
    /**
     * Handle the Alat "created" event.
     */
    public function created(Alat $alat): void
    {
        //
    }

    /**
     * Handle the Alat "updated" event.
     */
    public function updated(Alat $alat): void
    {
        //
    }

    /**
     * Handle the Alat "deleted" event.
     */
    public function deleted(Alat $alat): void
    {
        //
    }

    /**
     * Handle the Alat "restored" event.
     */
    public function restored(Alat $alat): void
    {
        //
    }

    /**
     * Handle the Alat "force deleted" event.
     */
    public function forceDeleted(Alat $alat): void
    {
        //
    }
}
