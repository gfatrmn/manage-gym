import React, { useEffect, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    AlertCircle,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    Moon,
    Sun,
    X,
} from 'lucide-react';
import { cn } from '../lib/format';

export function ThemeToggle({ className = '' }) {
    return null;
}

const BUTTON_VARIANTS = {
    primary:
        'bg-red-600 text-white shadow-sm shadow-red-600/20 hover:bg-red-700 focus:ring-red-500',
    secondary:
        'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-400',
    success:
        'bg-emerald-600 text-white hover:bg-emerald-500 focus:ring-emerald-500',
    danger:
        'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    warning:
        'bg-slate-800 text-white hover:bg-slate-900 focus:ring-slate-500',
    ghost:
        'text-slate-600 hover:bg-slate-100 focus:ring-slate-400',
};

const BUTTON_SIZES = {
    xs: 'px-2.5 py-1.5 text-xs rounded-lg',
    sm: 'px-3 py-2 text-xs sm:text-sm rounded-xl',
    md: 'px-4 py-2.5 text-sm rounded-xl',
    lg: 'px-5 py-3 text-base rounded-2xl',
};

export function Button({
    variant = 'primary',
    size = 'md',
    className = '',
    type = 'button',
    disabled = false,
    children,
    ...props
}) {
    return (
        <button
            type={type}
            disabled={disabled}
            className={cn(
                'inline-flex items-center justify-center gap-2 font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-slate-950',
                BUTTON_VARIANTS[variant] || BUTTON_VARIANTS.primary,
                BUTTON_SIZES[size] || BUTTON_SIZES.md,
                className
            )}
            {...props}
        >
            {children}
        </button>
    );
}

const BADGE_VARIANTS = {
    success: 'bg-emerald-50 text-emerald-700 ring-emerald-500/30',
    warning: 'bg-red-50 text-red-700 ring-red-500/20',
    danger: 'bg-red-50 text-red-700 ring-red-500/30',
    info: 'bg-slate-100 text-slate-700 ring-slate-500/20',
    purple: 'bg-slate-100 text-slate-700 ring-slate-500/20',
    neutral: 'bg-slate-100 text-slate-700 ring-slate-500/20',
};

export function Badge({ variant = 'neutral', className = '', children }) {
    return (
        <span
            className={cn(
                'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset',
                BADGE_VARIANTS[variant] || BADGE_VARIANTS.neutral,
                className
            )}
        >
            {children}
        </span>
    );
}

export function Card({ className = '', children, padding = 'p-5 sm:p-6' }) {
    return (
        <div
            className={cn(
                'rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900',
                padding,
                className
            )}
        >
            {children}
        </div>
    );
}

export function StatCard({
    title,
    value,
    subtitle,
    icon: Icon,
    accent = 'rose',
    className = '',
}) {
    const accents = {
        rose: 'bg-red-50 text-red-600',
        red: 'bg-red-50 text-red-600',
        emerald: 'bg-emerald-50 text-emerald-600',
        amber: 'bg-red-50 text-red-600',
        sky: 'bg-slate-100 text-slate-700',
        violet: 'bg-slate-100 text-slate-700',
    };

    return (
        <Card className={cn('relative overflow-hidden', className)}>
            <div className="flex items-start justify-between gap-4">
                <div className="min-w-0">
                    <p className="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        {title}
                    </p>
                    <p className="mt-2 truncate text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                        {value}
                    </p>
                    {subtitle && (
                        <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            {subtitle}
                        </p>
                    )}
                </div>
                {Icon && (
                    <div
                        className={cn(
                            'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
                            accents[accent] || accents.rose
                        )}
                    >
                        <Icon className="h-5 w-5" />
                    </div>
                )}
            </div>
        </Card>
    );
}

export function InputLabel({ htmlFor, required = false, children, className = '' }) {
    return (
        <label
            htmlFor={htmlFor}
            className={cn(
                'mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-600',
                className
            )}
        >
            {children}
            {required && <span className="ml-1 text-red-600">*</span>}
        </label>
    );
}

export function Input({ className = '', error, ...props }) {
    return (
        <input
            className={cn(
                'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20',
                error && 'border-red-600 focus:border-red-600 focus:ring-red-600/30',
                className
            )}
            {...props}
        />
    );
}

export function Select({ className = '', error, children, ...props }) {
    return (
        <select
            className={cn(
                'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20',
                error && 'border-red-600 focus:border-red-600 focus:ring-red-600/30',
                className
            )}
            {...props}
        >
            {children}
        </select>
    );
}

export function Textarea({ className = '', error, ...props }) {
    return (
        <textarea
            className={cn(
                'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20',
                error && 'border-red-600 focus:border-red-600 focus:ring-red-600/30',
                className
            )}
            {...props}
        />
    );
}

export function FieldError({ message }) {
    if (!message) return null;
    return <p className="mt-1 text-xs font-medium text-red-600">{message}</p>;
}

export function Modal({ open, onClose, title, subtitle, maxWidth = 'max-w-lg', children }) {
    useEffect(() => {
        if (!open) return undefined;
        const onKeyDown = (e) => {
            if (e.key === 'Escape') onClose?.();
        };
        window.addEventListener('keydown', onKeyDown);
        return () => window.removeEventListener('keydown', onKeyDown);
    }, [open, onClose]);

    if (!open) return null;

    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/65 p-4 backdrop-blur-sm"
            onClick={(e) => {
                if (e.target === e.currentTarget) onClose?.();
            }}
        >
            <div
                className={cn(
                    'w-full max-h-[90vh] overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900',
                    maxWidth
                )}
            >
                <div className="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h3 className="text-lg font-bold text-slate-900 dark:text-white">
                            {title}
                        </h3>
                        {subtitle && (
                            <p className="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                                {subtitle}
                            </p>
                        )}
                    </div>
                    <button
                        type="button"
                        onClick={onClose}
                        className="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                    >
                        <X className="h-5 w-5" />
                    </button>
                </div>
                {children}
            </div>
        </div>
    );
}

export function FlashBanner() {
    const { flash = {}, errors = {} } = usePage().props;
    const [dismissedKey, setDismissedKey] = useState(null);

    const errorList = Object.values(errors || {}).filter(Boolean);
    const activeKey = `${flash.status || ''}|${flash.error || ''}|${errorList[0] || ''}`;

    if (activeKey === '||' || dismissedKey === activeKey) return null;

    return (
        <div className="mb-5 space-y-2">
            {flash.status && (
                <div className="flex items-start justify-between gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200">
                    <div className="flex items-start gap-2.5">
                        <CheckCircle2 className="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" />
                        <span>{flash.status}</span>
                    </div>
                    <button
                        type="button"
                        onClick={() => setDismissedKey(activeKey)}
                        className="text-emerald-600 hover:opacity-75 dark:text-emerald-300"
                    >
                        <X className="h-4 w-4" />
                    </button>
                </div>
            )}

            {(flash.error || errorList.length > 0) && (
                <div className="flex items-start justify-between gap-3 rounded-2xl border border-red-500/30 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <div className="flex items-start gap-2.5">
                        <AlertCircle className="mt-0.5 h-4 w-4 shrink-0 text-red-600" />
                        <div className="space-y-1">
                            {flash.error && <p>{flash.error}</p>}
                            {errorList.length === 1 && <p>{errorList[0]}</p>}
                            {errorList.length > 1 && (
                                <ul className="list-disc pl-4">
                                    {errorList.map((err, idx) => (
                                        <li key={idx}>{err}</li>
                                    ))}
                                </ul>
                            )}
                        </div>
                    </div>
                    <button
                        type="button"
                        onClick={() => setDismissedKey(activeKey)}
                        className="text-red-600 hover:opacity-75"
                    >
                        <X className="h-4 w-4" />
                    </button>
                </div>
            )}
        </div>
    );
}

export function Pagination({ paginator }) {
    if (!paginator || !paginator.links || paginator.last_page <= 1) return null;

    return (
        <div className="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4 dark:border-slate-800">
            <p className="text-xs text-slate-500 dark:text-slate-400">
                Menampilkan <span className="font-semibold text-slate-700 dark:text-slate-200">{paginator.from || 0}</span>–
                <span className="font-semibold text-slate-700 dark:text-slate-200">{paginator.to || 0}</span> dari{' '}
                <span className="font-semibold text-slate-700 dark:text-slate-200">{paginator.total || 0}</span> data
            </p>
            <div className="flex flex-wrap items-center gap-1">
                {paginator.links.map((link, idx) => {
                    const isPrev = idx === 0;
                    const isNext = idx === paginator.links.length - 1;
                    const label = isPrev ? (
                        <ChevronLeft className="h-4 w-4" />
                    ) : isNext ? (
                        <ChevronRight className="h-4 w-4" />
                    ) : (
                        <span dangerouslySetInnerHTML={{ __html: link.label }} />
                    );

                    if (!link.url) {
                        return (
                            <span
                                key={idx}
                                className="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2.5 text-xs text-slate-400 dark:text-slate-600"
                            >
                                {label}
                            </span>
                        );
                    }

                    return (
                        <Link
                            key={idx}
                            href={link.url}
                            preserveScroll
                            className={cn(
                                'inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2.5 text-xs font-medium transition',
                                link.active
                                    ? 'bg-red-600 text-white'
                                    : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-100'
                            )}
                        >
                            {label}
                        </Link>
                    );
                })}
            </div>
        </div>
    );
}

export function EmptyState({ icon: Icon, title, description, action }) {
    return (
        <div className="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 px-6 py-12 text-center dark:border-slate-700">
            {Icon && (
                <div className="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                    <Icon className="h-6 w-6" />
                </div>
            )}
            <p className="text-sm font-semibold text-slate-900 dark:text-white">{title}</p>
            {description && (
                <p className="mt-1 max-w-md text-xs text-slate-500 dark:text-slate-400">
                    {description}
                </p>
            )}
            {action && <div className="mt-4">{action}</div>}
        </div>
    );
}

