<?php

return [
    //================= within budget <=3k and approver is not ceo ===============
    [
        'step_number'      => '1',
        'checker'          => 'first_reviewer',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'first reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 0,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '2',
        'checker'          => 'second_reviewer',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'second reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 0,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '3',
        'checker'          => 'budget_owner',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'budget owner',
        'version'          => 2,
        'approver_is_ceo'  => 0,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '4',
        'checker'          => 'approver',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'approver',
        'version'          => 2,
        'approver_is_ceo'  => 0,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '5',
        'checker'          => 'receiver',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'receiver',
        'version'          => 2,
        'approver_is_ceo'  => 0,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],
   //================= within budget <=3k and approver is ceo ===============
    [
        'step_number'      => '1',
        'checker'          => 'first_reviewer',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'first reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '2',
        'checker'          => 'second_reviewer',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'second reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '3',
        'checker'          => 'budget_owner',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'budget owner',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '4',
        'checker'          => 'md_office',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'MD office',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '5',
        'checker'          => 'approver_ceo',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'CEO',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '6',
        'checker'          => 'receiver',
        'within_budget'    => 'Y',
        'amount_request'   => '<=3000',
        'step_description' => 'receiver',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],
    //================= within budget <=5k and approver is not ceo ===============
    [
        'step_number'      => '1',
        'checker'          => 'first_reviewer',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'first reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 0,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '2',
        'checker'          => 'second_reviewer',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'second reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 0,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '3',
        'checker'          => 'budget_owner',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'budget owner',
        'version'          => 2,
        'approver_is_ceo'  => 0,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '4',
        'checker'          => 'approver',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'approver',
        'version'          => 2,
        'approver_is_ceo'  => 0,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '5',
        'checker'          => 'receiver',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'receiver',
        'version'          => 2,
        'approver_is_ceo'  => 0,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],
    //================= within budget <=5k and approver is ceo ===============
    [
        'step_number'      => '1',
        'checker'          => 'first_reviewer',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'first reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '2',
        'checker'          => 'second_reviewer',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'second reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '3',
        'checker'          => 'budget_owner',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'budget owner',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '4',
        'checker'          => 'md_office',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'MD office',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '5',
        'checker'          => 'approver_ceo',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'CEO',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '6',
        'checker'          => 'receiver',
        'within_budget'    => 'Y',
        'amount_request'   => '<=5000',
        'step_description' => 'receiver',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],
    //================= within budget >5k ===============
    [
        'step_number'      => '1',
        'checker'          => 'first_reviewer',
        'within_budget'    => 'Y',
        'amount_request'   => '>5000',
        'step_description' => 'first reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '2',
        'checker'          => 'second_reviewer',
        'within_budget'    => 'Y',
        'amount_request'   => '>5000',
        'step_description' => 'second reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '3',
        'checker'          => 'budget_owner',
        'within_budget'    => 'Y',
        'amount_request'   => '>5000',
        'step_description' => 'budget owner',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '4',
        'checker'          => 'md_office',
        'within_budget'    => 'Y',
        'amount_request'   => '>5000',
        'step_description' => 'MD office',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '5',
        'checker'          => 'approver_ceo',
        'within_budget'    => 'Y',
        'amount_request'   => '>5000',
        'step_description' => 'CEO',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '6',
        'checker'          => 'receiver',
        'within_budget'    => 'Y',
        'amount_request'   => '>5000',
        'step_description' => 'receiver',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],
    //================= within budget >5k ===============
    [
        'step_number'      => '1',
        'checker'          => 'first_reviewer',
        'within_budget'    => 'N',
        'amount_request'   => '0',
        'step_description' => 'first reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '2',
        'checker'          => 'second_reviewer',
        'within_budget'    => 'N',
        'amount_request'   => '0',
        'step_description' => 'second reviewer',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '3',
        'checker'          => 'approver_cfo',
        'within_budget'    => 'N',
        'amount_request'   => '0',
        'step_description' => 'Approver cfo',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '4',
        'checker'          => 'md_office',
        'within_budget'    => 'N',
        'amount_request'   => '0',
        'step_description' => 'MD office',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '5',
        'checker'          => 'approver_ceo',
        'within_budget'    => 'N',
        'amount_request'   => '0',
        'step_description' => 'CEO',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],[
        'step_number'      => '6',
        'checker'          => 'receiver',
        'within_budget'    => 'N',
        'amount_request'   => '0',
        'step_description' => 'receiver',
        'version'          => 2,
        'approver_is_ceo'  => 1,
        'req_name'         => '1',
        'notification_type'=>'Procurement'
    ],
];
