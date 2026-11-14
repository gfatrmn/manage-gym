import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Camera, Clock, KeyRound, MessageSquarePlus, Save, ShieldCheck, User } from 'lucide-react';
import MemberLayout from '../../Layouts/MemberLayout';
import {
    Badge,
    Button,
    Card,
    FieldError,
    Input,
    InputLabel,
    Select,
    Textarea,
} from '../../Components/UI';

export default function Profile({
    user,
    member,
    totalCheckins = 0,
    membershipStatus = 'member',
    photoChangesRemaining = 3,
    pendingPhotoRequest = null,
}) {
    const profileForm = useForm({
        name: member?.full_name || user?.name || '',
        email: member?.email || user?.email || '',
        phone: member?.phone || '',
        gender: member?.gender || 'male',
        birth_date: member?.birth_date ? member.birth_date.split('T')[0] : '',
        address: member?.address || '',
    });

    const photoForm = useForm({
        profile_photo: null,
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const feedbackForm = useForm({
        category: 'Fasilitas',
        message: '',
    });

    const submitProfile = (e) => {
        e.preventDefault();
        profileForm.post('/member/profile');
    };

    const submitPhoto = (e) => {
        e.preventDefault();
        photoForm.post('/member/profile/photo', {
            forceFormData: true,
            onSuccess: () => photoForm.reset(),
        });
    };

    const submitPassword = (e) => {
        e.preventDefault();
        passwordForm.post('/member/password', {
            onSuccess: () => passwordForm.reset(),
        });
    };

    const submitFeedback = (e) => {
        e.preventDefault();
        feedbackForm.post('/member/feedback', {
            onSuccess: () => feedbackForm.reset('message'),
        });
    };

    return (
        <MemberLayout title="Profil Saya & Pengaturan Akun" active="profile">
            <div className="grid gap-6 lg:grid-cols-3">
                {/* Foto Profil & ID Member */}
                <div className="space-y-6">
                    <Card>
                        <h2 className="text-base font-bold text-slate-900 dark:text-white">
                            Foto Profil & Kartu Member
                        </h2>
                        <div className="mt-4 flex flex-col items-center text-center">
                            {member?.profile_photo_url ? (
                                <img
                                    src={member.profile_photo_url}
                                    alt={user?.name}
                                    className="h-28 w-28 rounded-3xl border-2 border-red-500/30 object-cover shadow"
                                />
                            ) : (
                                <div className="flex h-28 w-28 items-center justify-center rounded-3xl bg-red-600 text-3xl font-extrabold text-white shadow-md">
                                    {member?.profile_initials || user?.name?.charAt(0) || 'AG'}
                                </div>
                            )}

                            <p className="mt-3 text-base font-bold text-slate-900">
                                {member?.full_name || user?.name}
                            </p>
                            <p className="font-mono text-xs font-bold text-red-600">
                                {member?.checkin_code || 'MEMBER'}
                            </p>

                            <div className="mt-2 flex items-center gap-2">
                                <Badge variant="success">{membershipStatus}</Badge>
                                <Badge variant="neutral">{totalCheckins} Check-in</Badge>
                            </div>

                            <p className="mt-3 text-xs text-slate-500">
                                Sisa kuota ganti foto langsung:{' '}
                                <strong className="text-slate-900">
                                    {photoChangesRemaining}x
                                </strong>
                            </p>

                            {pendingPhotoRequest && (
                                <div className="mt-3 flex w-full items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-3 text-left text-xs text-red-800">
                                    <Clock className="h-4 w-4 shrink-0 text-red-600" />
                                    <span>
                                        Pengajuan foto tanggal {pendingPhotoRequest.created_at} sedang menunggu persetujuan admin.
                                    </span>
                                </div>
                            )}

                            <form onSubmit={submitPhoto} className="mt-4 w-full space-y-3">
                                <input
                                    type="file"
                                    accept="image/png,image/jpeg,image/webp"
                                    onChange={(e) => photoForm.setData('profile_photo', e.target.files?.[0] || null)}
                                    className="block w-full rounded-xl border border-slate-200 p-2 text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-red-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-red-600"
                                />
                                <FieldError message={photoForm.errors.profile_photo} />
                                <Button
                                    type="submit"
                                    variant="secondary"
                                    disabled={photoForm.processing || !photoForm.data.profile_photo}
                                    className="w-full text-xs"
                                >
                                    <Camera className="h-4 w-4" />
                                    {photoForm.processing ? 'Mengunggah...' : 'Unggah Foto Baru'}
                                </Button>
                            </form>
                        </div>
                    </Card>

                    {/* Ubah Password */}
                    <Card>
                        <div className="flex items-center gap-2 border-b border-slate-100 pb-3 dark:border-slate-800">
                            <KeyRound className="h-4 w-4 text-rose-600 dark:text-rose-400" />
                            <h2 className="text-base font-bold text-slate-900 dark:text-white">
                                Ubah Password
                            </h2>
                        </div>
                        <form onSubmit={submitPassword} className="mt-4 space-y-3">
                            <div>
                                <InputLabel htmlFor="current_password" required>
                                    Password Saat Ini
                                </InputLabel>
                                <Input
                                    id="current_password"
                                    type="password"
                                    value={passwordForm.data.current_password}
                                    onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                    error={passwordForm.errors.current_password}
                                    required
                                />
                                <FieldError message={passwordForm.errors.current_password} />
                            </div>

                            <div>
                                <InputLabel htmlFor="new_password" required>
                                    Password Baru
                                </InputLabel>
                                <Input
                                    id="new_password"
                                    type="password"
                                    value={passwordForm.data.password}
                                    onChange={(e) => passwordForm.setData('password', e.target.value)}
                                    error={passwordForm.errors.password}
                                    required
                                />
                                <FieldError message={passwordForm.errors.password} />
                            </div>

                            <div>
                                <InputLabel htmlFor="password_confirmation" required>
                                    Konfirmasi Password Baru
                                </InputLabel>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    value={passwordForm.data.password_confirmation}
                                    onChange={(e) => passwordForm.setData('password_confirmation', e.target.value)}
                                    error={passwordForm.errors.password_confirmation}
                                    required
                                />
                                <FieldError message={passwordForm.errors.password_confirmation} />
                            </div>

                            <Button
                                type="submit"
                                disabled={passwordForm.processing}
                                className="w-full text-xs"
                            >
                                <ShieldCheck className="h-4 w-4" />
                                {passwordForm.processing ? 'Menyimpan...' : 'Perbarui Password'}
                            </Button>
                        </form>
                    </Card>
                </div>

                {/* Edit Profil Data Diri & Feedback */}
                <div className="space-y-6 lg:col-span-2">
                    <Card>
                        <h2 className="text-base font-bold text-slate-900 dark:text-white">
                            Data Diri & Kontak Member
                        </h2>
                        <form onSubmit={submitProfile} className="mt-4 space-y-4">
                            <div>
                                <InputLabel htmlFor="name" required>
                                    Nama Lengkap
                                </InputLabel>
                                <Input
                                    id="name"
                                    value={profileForm.data.name}
                                    onChange={(e) => profileForm.setData('name', e.target.value)}
                                    error={profileForm.errors.name}
                                    required
                                />
                                <FieldError message={profileForm.errors.name} />
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel htmlFor="email" required>
                                        Alamat Email
                                    </InputLabel>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={profileForm.data.email}
                                        onChange={(e) => profileForm.setData('email', e.target.value)}
                                        error={profileForm.errors.email}
                                        required
                                    />
                                    <FieldError message={profileForm.errors.email} />
                                </div>
                                <div>
                                    <InputLabel htmlFor="phone">Nomor WhatsApp / HP</InputLabel>
                                    <Input
                                        id="phone"
                                        value={profileForm.data.phone}
                                        onChange={(e) => profileForm.setData('phone', e.target.value)}
                                        placeholder="08xxxxxxxxxx"
                                        error={profileForm.errors.phone}
                                    />
                                    <FieldError message={profileForm.errors.phone} />
                                </div>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel htmlFor="gender">Jenis Kelamin</InputLabel>
                                    <Select
                                        id="gender"
                                        value={profileForm.data.gender}
                                        onChange={(e) => profileForm.setData('gender', e.target.value)}
                                    >
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                    </Select>
                                </div>
                                <div>
                                    <InputLabel htmlFor="birth_date">Tanggal Lahir</InputLabel>
                                    <Input
                                        id="birth_date"
                                        type="date"
                                        value={profileForm.data.birth_date}
                                        onChange={(e) => profileForm.setData('birth_date', e.target.value)}
                                        error={profileForm.errors.birth_date}
                                    />
                                    <FieldError message={profileForm.errors.birth_date} />
                                </div>
                            </div>

                            <div>
                                <InputLabel htmlFor="address">Alamat Domisili</InputLabel>
                                <Textarea
                                    id="address"
                                    rows={2}
                                    value={profileForm.data.address}
                                    onChange={(e) => profileForm.setData('address', e.target.value)}
                                    placeholder="Alamat tempat tinggal member..."
                                    error={profileForm.errors.address}
                                />
                                <FieldError message={profileForm.errors.address} />
                            </div>

                            <Button type="submit" disabled={profileForm.processing} className="gap-2">
                                <Save className="h-4 w-4" />
                                {profileForm.processing ? 'Menyimpan...' : 'Simpan Perubahan Data Diri'}
                            </Button>
                        </form>
                    </Card>

                    <Card>
                        <h2 className="text-base font-bold text-slate-900 dark:text-white">
                            Kirim Kritik & Saran ke Pemilik Gym
                        </h2>
                        <form onSubmit={submitFeedback} className="mt-4 space-y-4">
                            <div>
                                <InputLabel htmlFor="category" required>
                                    Kategori Masukan
                                </InputLabel>
                                <Select
                                    id="category"
                                    value={feedbackForm.data.category}
                                    onChange={(e) => feedbackForm.setData('category', e.target.value)}
                                >
                                    <option value="Fasilitas">Fasilitas & Alat Gym</option>
                                    <option value="Pelayanan">Pelayanan Staff & Kasir</option>
                                    <option value="Kebersihan">Kebersihan & Kenyamanan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </Select>
                            </div>
                            <div>
                                <InputLabel htmlFor="message" required>
                                    Pesan Anda
                                </InputLabel>
                                <Textarea
                                    id="message"
                                    rows={3}
                                    value={feedbackForm.data.message}
                                    onChange={(e) => feedbackForm.setData('message', e.target.value)}
                                    placeholder="Tuliskan masukan, keluhan, atau saran perbaikan..."
                                    error={feedbackForm.errors.message}
                                    required
                                />
                                <FieldError message={feedbackForm.errors.message} />
                            </div>
                            <Button type="submit" variant="secondary" disabled={feedbackForm.processing}>
                                <MessageSquarePlus className="h-4 w-4" />
                                {feedbackForm.processing ? 'Mengirim...' : 'Kirim Kritik & Saran'}
                            </Button>
                        </form>
                    </Card>
                </div>
            </div>
        </MemberLayout>
    );
}
