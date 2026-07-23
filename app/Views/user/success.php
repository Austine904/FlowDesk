<?php
$pageTitle = 'User Created Successfully';
$title = 'User Created Successfully';
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Registration Successful!</h1>
        <p class="text-gray-500 mb-8">The user has been successfully registered in the system.</p>

        <div class="flex gap-3 justify-center">
            <a href="<?= base_url('user/add_step1') ?>"
               class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                Add Another User
            </a>
            <a href="<?= base_url('admin/users') ?>"
               class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                Go to Users
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
