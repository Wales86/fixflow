<?php

return [
    'statuses' => [
        'new' => 'New',
        'diagnosis' => 'Diagnosis',
        'awaiting_contact' => 'Awaiting Contact',
        'awaiting_parts' => 'Awaiting Parts',
        'in_progress' => 'In Progress',
        'ready_for_pickup' => 'Ready for Pickup',
        'closed' => 'Closed',
    ],

    'messages' => [
        'created' => 'Repair order has been created',
        'updated' => 'Repair order has been updated',
        'status_updated' => 'Order status has been changed',
        'status_update_failed' => 'Failed to change status',
        'deleted' => 'Repair order has been deleted',
        'cannot_delete_with_time_entries' => 'Cannot delete repair order with time entries',
    ],

    'pages' => [
        'edit' => [
            'title' => 'Edit repair order',
            'breadcrumb' => 'Edit',
        ],
    ],
];
