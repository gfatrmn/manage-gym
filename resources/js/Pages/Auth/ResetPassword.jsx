import React from 'react';
import { useForm } from '@inertiajs/react';
import { KeyRound } from 'lucide-react';
import GuestLayout from '../../Layouts/GuestLayout';
import { Button, Card, FieldError, Input, InputLabel } from '../../Components/UI';

export default function ResetPassword({ token, email = '' }) {
    const { data, setData, post, processing, errors } = useForm({
        token: token || '',
        email: email || '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/reset-password');
    };

    return (
        <GuestLayout
            title="Buat Password Baru"
            subtitle="Masukkan password baru untuk mengamankan akun member Anda"
        >
            <Card>
                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <InputLabel htmlFor="email" required>
                            Email
                        </InputLabel>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            error={errors.email}
                        />
                        <FieldError message={errors.email} />
                    </div>

                    <div>
                        <InputLabel htmlFor="password" required>
                            Password Baru
                        </InputLabel>
                        <Input
                            id="password"
                            type="password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="Minimal 6 karakter"
                            error={errors.password}
                        />
                        <FieldError message={errors.password} />
                    </div>

                    <div>
                        <InputLabel htmlFor="password_confirmation" required>
                            Konfirmasi Password Baru
                        </InputLabel>
                        <Input
                            id="password_confirmation"
                            type="password"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                        />
                    </div>

                    <Button type="submit" disabled={processing} className="w-full">
                        <KeyRound className="h-4 w-4" />
                        {processing ? 'Menyimpan...' : 'Simpan Password Baru'}
                    </Button>
                </form>
            </Card>
        </GuestLayout>
    );
}

