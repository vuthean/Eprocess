<?php

namespace Database\Seeders;

use App\Models\Activitydescription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activities = [[
            'activity_code' => 'A001',
            'activity_type' => 'Submitted',
            'activity_description' => 'Submit Request',
        ],[
            'activity_code' => 'A002',
            'activity_type' => 'Approved',
            'activity_description' => 'Approve Request',
        ],[
            'activity_code' => 'A003',
            'activity_type' => 'Rejected',
            'activity_description' => 'Reject Request',
        ],[
            'activity_code' => 'A004',
            'activity_type' => 'Assign Back',
            'activity_description' => 'Assign Back Request',
        ],[
            'activity_code' => 'A005',
            'activity_type' => 'Resubmitted',
            'activity_description' => 'Resubmitted Request',
        ],[
            'activity_code' => 'A006',
            'activity_type' => 'Closed',
            'activity_description' => 'Closed Request',
        ],[
            'activity_code' => 'A007',
            'activity_type' => 'Assign',
            'activity_description' => 'Assign Back',
        ],[
            'activity_code' => 'A008',
            'activity_type' => 'Query',
            'activity_description' => 'Query',
        ],[
            'activity_code' => 'A009',
            'activity_type' => 'Transfer',
            'activity_description' => 'Transfer',
        ],[
            'activity_code' => 'A010',
            'activity_type' => 'Paid_yes',
            'activity_description' => 'Set request paid to YES',
        ],[
            'activity_code' => 'A011',
            'activity_type' => 'Paid_no',
            'activity_description' => 'Set request paid to NO',
        ],[
            'activity_code' => 'A012',
            'activity_type' => 'Paid_cancel',
            'activity_description' => 'Set request paid to CANCEL',
        ],];

        DB::transaction(function() use($activities){
            foreach($activities as $activity){
                $isExist = Activitydescription::firstWhere('activity_code',$activity['activity_code']);
                if(!$isExist){
                    Activitydescription::create($activity);
                }
            }
        });
    }
}
