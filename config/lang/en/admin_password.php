<?php
// Admin — Password Change page (admin.password.*).
return [
    // ── Validation / status messages ──
    'admin.password.err_current'    => 'The current password is incorrect.',
    'admin.password.err_min_length' => 'The new password must be at least 6 characters long.',
    'admin.password.err_mismatch'   => 'The new password and confirmation do not match.',
    'admin.password.msg_changed'    => 'Password changed successfully.',

    // ── Page chrome ──
    'admin.password.heading'        => 'Change password',
    'admin.password.help_title'     => '🔑 Security tips for your password:',
    'admin.password.help_length'    => 'Use at least 8 characters (minimum 6 required).',
    'admin.password.help_mix'       => 'Mix upper- and lowercase letters, numbers and special characters.',
    'admin.password.help_unique'    => 'Do not use the same password for other accounts.',
    'admin.password.help_share'     => 'Do not share your password with anyone via email or message.',

    // ── Form ──
    'admin.password.legend'         => 'Change password',
    'admin.password.field_current'  => 'Current password',
    'admin.password.field_new'      => 'New password',
    'admin.password.field_confirm'  => 'Confirm new password',
    'admin.password.hint_min'       => 'At least 6 characters.',
    'admin.password.submit'         => 'Change password',
];
