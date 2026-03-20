<?php

namespace App\Filament\Admin\Resources\Donors\Pages;

use App\Filament\Admin\Resources\Donors\DonorResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateDonor extends CreateRecord
{
    protected static string $resource = DonorResource::class;
}
