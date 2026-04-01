<?php

return [

    'system_name' => 'ALIMS - Airport Lost & Found Management System',

    'modules' => [
        'Lost Reports' => 'Create passenger lost reports, review report details, view report history, edit or delete LOST reports, and perform candidate matching.',
        'Found Items' => 'Register found items, view item details, edit or delete unclaimed items, and review removed or claimed records.',
        'Claim History' => 'Process claim appointments, confirmation, handover flow, and completed claim history.',
        'Inventory Map' => 'View storage slots, move items, remove items, mark service slots, and track inventory location history.',
        'AI Help Assistant' => 'Explain system statuses, workflows, and module navigation for staff users.',
        'Staff Management' => 'Admin manages staff accounts and staff records.',
        'Audit Logs' => 'Admin reviews recorded actions and history logs.',
        'Vouchers' => 'Manage voucher records and redemption-related functions.',
        'Analytics Chart' => 'View dashboard charts and overview insights.',
    ],

    'status_meanings' => [
        'LOST' => 'A passenger lost report has been created but no confirmed match has been made yet.',
        'Matched' => 'A lost report and found item have been linked together for claim processing.',
        'Claimed' => 'The handover process has been completed successfully.',
        'Unclaimed' => 'A found item exists in the system but has not been matched yet.',
        'Removed' => 'A found item was removed from active inventory and kept as a historical record.',
        'Reschedule Requested' => 'The passenger requested a different appointment time and staff should review the claim flow again.',
    ],

    'rules' => [
        'lost_reports' => [
            'LOST reports can be edited or deleted.',
            'Matched or Reschedule Requested reports should use Undo Match first before changing back.',
            'Claimed reports are view only.',
        ],
        'found_items' => [
            'Unclaimed items can be edited or deleted.',
            'Matched items should use Undo Match first before changing details.',
            'Claimed items are view only.',
            'Removed items are historical records and mainly view only.',
        ],
    ],

    'workflow' => [
        'Lost report created',
        'Found item may be matched',
        'Claim process may start',
        'Appointment may be scheduled',
        'Passenger confirms',
        'Handover completes the case',
    ],

    'faq' => [
        [
            'keywords' => ['matched status', 'what is matched', 'matched mean'],
            'answer' => 'Matched means the lost report and found item have been linked together. Staff can continue to Manage Claim or use Undo Match if the match is incorrect.',
        ],
        [
            'keywords' => ['claimed status', 'what is claimed', 'claimed mean'],
            'answer' => 'Claimed means the handover process has already been completed. Claimed records are view only and remain as completed history.',
        ],
        [
            'keywords' => ['removed status', 'what is removed', 'removed mean'],
            'answer' => 'Removed means the item was taken out of active inventory and is now kept only as a historical record. It can still be viewed in the system but is no longer available for matching or claiming.',
        ],
        [
            'keywords' => ['undo match', 'unmatch'],
            'answer' => 'To undo a match, open the matched record action menu and choose Undo Match. This returns the lost report back to LOST and the found item back to Unclaimed.',
        ],
        [
            'keywords' => ['claim process', 'manage claim', 'how does claim work'],
            'answer' => 'The normal claim flow is: Lost Report created, Matched, Manage Claim, Appointment scheduled, Passenger confirms, Handover completed, Claimed.',
        ],
        [
            'keywords' => ['edit matched item', 'can i edit matched', 'delete matched item'],
            'answer' => 'Matched records should not be edited directly. Use Undo Match first, then the lost report returns to LOST and the found item returns to Unclaimed, so editing can be done safely.',
        ],
    ],
];