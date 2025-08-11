<?php

namespace Database\Seeders;

use App\Livewire\Parkings\ParkingManagement;
use App\Models\Apartment;
use Illuminate\Database\Seeder;
use App\Models\ApartmentManagement;
use App\Models\ApartmentParking;
use App\Models\Floor;
use App\Models\ParkingManagementSetting;
use App\Models\Tower;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ApartmentOwner;

class ApartmentManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {

        $floorId = Floor::where('society_id', $society->id)->first()->id;

        $ownerId = User::query()->whereHas('role', function ($q) {
            $q->where('display_name', 'Owner');
        })->where('society_id', $society->id)->pluck('id')->toArray();

        $towerId = Tower::where('society_id', $society->id)->first()->id;
        $parking = ParkingManagementSetting::where('society_id', $society->id)->get()->pluck('id')->toArray();
        $apartmentId = Apartment::where('society_id', $society->id)->first()->id;

        $apartment = new ApartmentManagement();
        $apartment->apartment_number = '101';
        $apartment->apartment_area = 2;
        $apartment->floor_id = $floorId;
        $apartment->tower_id = $towerId;
        $apartment->apartment_id = $apartmentId;
        $apartment->status = 'occupied';
        $apartment->user_id = $ownerId[0];
        $apartment->apartment_area_unit = "sqft";
        $apartment->society_id = $society->id;
        $apartment->save();
        if ($apartment->user_id) {
            ApartmentOwner::insert([
                'user_id' => $apartment->user_id,
                'apartment_id' => $apartment->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $apartmentParking = new ApartmentParking();
        $apartmentParking->parking_id = $parking[0];
        $apartmentParking->apartment_management_id = $apartment->id;
        $apartmentParking->save();

        $apartment = new ApartmentManagement();
        $apartment->apartment_number = '102';
        $apartment->apartment_area = 2;
        $apartment->floor_id = $floorId;
        $apartment->tower_id = $towerId;
        $apartment->apartment_id = $apartmentId;
        $apartment->user_id = $ownerId[1];
        $apartment->status = 'occupied';
        $apartment->apartment_area_unit = "sqft";
        $apartment->society_id = $society->id;
        $apartment->save();
        if ($apartment->user_id) {
            ApartmentOwner::insert([
                'user_id' => $apartment->user_id,
                'apartment_id' => $apartment->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        $apartmentParking = new ApartmentParking();
        $apartmentParking->parking_id = $parking[1];
        $apartmentParking->apartment_management_id = $apartment->id;
        $apartmentParking->save();

        $apartment = new ApartmentManagement();
        $apartment->apartment_number = '103';
        $apartment->apartment_area = 2;
        $apartment->floor_id = $floorId;
        $apartment->tower_id = $towerId;
        $apartment->apartment_id = $apartmentId;
        $apartment->status = 'rented';
        $apartment->user_id = null;
        $apartment->apartment_area_unit = "sqft";
        $apartment->society_id = $society->id;
        $apartment->save();
        if ($apartment->user_id) {
            ApartmentOwner::insert([
                'user_id' => $apartment->user_id,
                'apartment_id' => $apartment->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        $apartmentParking = new ApartmentParking();
        $apartmentParking->parking_id = $parking[2];
        $apartmentParking->apartment_management_id = $apartment->id;
        $apartmentParking->save();

        $apartment = new ApartmentManagement();
        $apartment->apartment_number = '104';
        $apartment->apartment_area = 2;
        $apartment->floor_id = $floorId;
        $apartment->tower_id = $towerId;
        $apartment->apartment_id = $apartmentId;
        $apartment->status = 'occupied';
        $apartment->user_id = $ownerId[2];
        $apartment->apartment_area_unit = "sqft";
        $apartment->society_id = $society->id;
        $apartment->save();
        if ($apartment->user_id) {
            ApartmentOwner::insert([
                'user_id' => $apartment->user_id,
                'apartment_id' => $apartment->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $apartmentParking = new ApartmentParking();
        $apartmentParking->parking_id = $parking[3];
        $apartmentParking->apartment_management_id = $apartment->id;
        $apartmentParking->save();
    }

}
