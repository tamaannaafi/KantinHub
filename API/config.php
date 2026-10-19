<?php
// ============================================================
// PENGATURAN KANTINHUB
// Ini satu-satunya file yang perlu kamu ubah kalau setting-nya beda.
// ============================================================

// --- Koneksi database (default XAMPP: user root, password kosong) ---
const DB_HOST = '127.0.0.1';
const DB_NAME = 'kantinhub';
const DB_USER = 'root';
const DB_PASS = '';

// --- Akun admin ---
// Password TIDAK disimpan polos, tapi dalam bentuk hash (bcrypt).
// Hash di bawah ini adalah untuk password admin lama dari script.js.
// Segera ganti! Cara membuat hash baru ada di penjelasan.
// PENTING: pakai tanda kutip TUNGGAL ' ' supaya tanda $ tidak dianggap variabel.
const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD_HASH = '$2y$12$K7xN0NII1vSb6yRMwgaUi.lseo/.nQck/HpjohNmMU/oBZF0ElHWG';

// --- Mode debug ---
// true  = pesan error asli MySQL ditampilkan (enak saat belajar/develop)
// false = pesan umum saja (wajib saat sudah online)
const DEBUG = true;
