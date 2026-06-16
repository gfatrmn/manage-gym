@extends('admin.layout')

@section('content')
    <style>
        body.page-cashier-dashboard {
            min-height: 100vh;
            overflow-x: hidden;
        }

        body.page-cashier-dashboard > .container-fluid {
            width: 100%;
            max-width: none;
            min-height: 100vh;
            padding: 0 !important;
        }

        body.page-cashier-dashboard > .container-fluid > .row {
            --bs-gutter-x: 0;
            --bs-gutter-y: 0;
            min-height: 100vh;
            margin: 0;
        }

        body.page-cashier-dashboard main.col-12 {
            width: 100%;
            max-width: none;
            min-height: 100vh;
            flex: 0 0 100%;
            padding: clamp(1rem, 2vw, 1.6rem);
        }

        .cashier-page {
            display: grid;
            width: 100%;
            min-height: calc(100vh - clamp(1rem, 2vw, 1.6rem) * 2);
            gap: clamp(.9rem, 1.4vw, 1.25rem);
            max-width: none;
            margin: 0;
        }

        .cashier-hero {
            position: relative;
            min-height: 340px;
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr);
            gap: 1rem;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 1.35rem;
            --hero-x: 50%;
            --hero-y: 50%;
            background:
                linear-gradient(115deg, rgba(8,8,10,.96) 0%, rgba(12,12,14,.8) 50%, rgba(40,10,14,.54) 100%),
                url('https://images.unsplash.com/photo-1576678927484-cc907957088c?auto=format&fit=crop&w=1800&q=85') center/cover no-repeat;
            box-shadow: var(--shadow);
            isolation: isolate;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .cashier-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(120deg, rgba(0,0,0,.8), transparent 72%);
            pointer-events: none;
            z-index: 0;
        }

        .cashier-hero::after {
            content: '';
            position: absolute;
            width: 340px;
            height: 340px;
            left: var(--hero-x);
            top: var(--hero-y);
            border-radius: 999px;
            background:
                radial-gradient(circle, rgba(255,66,74,.28) 0%, rgba(255,66,74,.14) 34%, transparent 70%);
            opacity: 0;
            transform: translate(-50%, -50%);
            pointer-events: none;
            transition: opacity .18s ease;
            z-index: 0;
        }

        .cashier-hero:hover {
            border-color: rgba(255,66,74,.28);
            box-shadow: var(--shadow), 0 0 0 1px rgba(255,66,74,.08);
        }

        .cashier-hero:hover::after {
            opacity: 1;
        }

        .cashier-hero-main,
        .cashier-hero-side {
            position: relative;
            z-index: 1;
            padding: clamp(1.25rem, 3vw, 2rem);
        }

        .cashier-hero-main {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 2rem;
        }

        .cashier-kicker {
            display: inline-flex;
            align-items: center;
            gap: .65rem;
            color: #ffd6d8;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .brand-logo-inline {
            width: 48px;
            height: auto;
            object-fit: contain;
            border-radius: 0.85rem;
            box-shadow: 0 12px 30px rgba(255, 66, 74, 0.15);
        }

        .cashier-kicker::before {
            content: '';
            width: 2rem;
            height: 2px;
            border-radius: 999px;
            background: #ff424a;
        }

        .cashier-hero-title {
            max-width: 12ch;
            margin: .9rem 0 0;
            color: #fff;
            font-size: clamp(2.25rem, 5vw, 4.9rem);
            line-height: .96;
            font-weight: 800;
            letter-spacing: 0;
        }

        .cashier-hero-copy {
            max-width: 42rem;
            margin-top: 1rem;
            color: rgba(255,255,255,.78);
            font-size: 1rem;
            line-height: 1.7;
        }

        .cashier-actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .8rem;
            max-width: 760px;
        }

        .cashier-action {
            min-height: 74px;
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .9rem 1rem;
            border: 1px solid rgba(255,255,255,.13);
            border-radius: 1rem;
            color: #fff;
            background: rgba(255,255,255,.07);
            text-decoration: none;
            font-weight: 800;
            backdrop-filter: blur(16px);
            transition: transform .18s ease, border-color .18s ease, background .18s ease;
        }

        .cashier-action:hover {
            color: #fff;
            transform: translateY(-2px);
            border-color: rgba(255,66,74,.45);
            background: rgba(255,66,74,.15);
        }

        .cashier-action.primary {
            background: linear-gradient(135deg, #ff4b53, #d80f27);
            border-color: rgba(255,255,255,.2);
            box-shadow: 0 18px 42px rgba(216,15,39,.28);
        }

        .cashier-action-icon,
        .cashier-stat-icon,
        .cashier-mini-icon {
            width: 2.5rem;
            height: 2.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .8rem;
            flex: 0 0 auto;
            background: rgba(255,255,255,.13);
        }

        .cashier-hero-side {
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 1rem;
        }

        .shift-card,
        .cashier-image-card {
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 1.1rem;
            background: rgba(10,10,12,.58);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
            backdrop-filter: blur(18px);
        }

        .shift-card {
            padding: 1.25rem;
        }

        .shift-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .shift-status {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            color: #bbf7d0;
            font-size: .8rem;
            font-weight: 800;
        }

        .shift-status::before {
            content: '';
            width: .6rem;
            height: .6rem;
            border-radius: 999px;
            background: #22c55e;
            box-shadow: 0 0 0 .35rem rgba(34,197,94,.12);
        }

        .shift-time {
            color: #fff;
            font-size: clamp(1.75rem, 3vw, 2.45rem);
            line-height: 1.05;
            font-weight: 800;
        }

        .shift-countdown {
            margin-top: .65rem;
            color: rgba(255,255,255,.74);
            font-weight: 700;
        }

        .cashier-image-card {
            min-height: 150px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: end;
            padding: 1rem;
            background:
                linear-gradient(180deg, transparent, rgba(0,0,0,.78)),
                url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=900&q=85') center/cover no-repeat;
        }

        .cashier-image-card strong {
            color: #fff;
            font-size: 1rem;
        }

        .cashier-image-card span {
            display: block;
            color: rgba(255,255,255,.7);
            font-size: .84rem;
            line-height: 1.45;
        }

        .cashier-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .cashier-stat {
            min-height: 142px;
            padding: 1.15rem;
            border: 1px solid var(--border);
            border-radius: 1.15rem;
            background: var(--surface);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            transition: transform .18s ease, border-color .18s ease;
        }

        .cashier-stat:hover {
            transform: translateY(-2px);
            border-color: rgba(255,66,74,.28);
        }

        .cashier-stat-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .85rem;
            margin-bottom: 1.2rem;
        }

        .cashier-stat-label {
            color: var(--text-muted);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .1em;
            line-height: 1.35;
            text-transform: uppercase;
        }

        .cashier-stat-value {
            color: var(--text-main);
            font-size: clamp(1.7rem, 3vw, 2.25rem);
            line-height: 1;
            font-weight: 800;
        }

        .cashier-stat-note {
            display: inline-flex;
            margin-top: .8rem;
            padding: .35rem .62rem;
            border-radius: 999px;
            color: #ffc8cc;
            background: rgba(255,66,74,.12);
            font-size: .74rem;
            font-weight: 800;
        }

        .cashier-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, .65fr);
            gap: 1rem;
            align-items: start;
        }

        .cashier-panel {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 1.15rem;
            background: var(--surface);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
        }

        .cashier-panel.elevated {
            background:
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)),
                var(--surface);
        }

        .cashier-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1rem 1.15rem;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,.025);
        }

        .cashier-panel-title {
            display: flex;
            align-items: center;
            gap: .8rem;
        }

        .cashier-panel-title-icon {
            width: 2.45rem;
            height: 2.45rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .85rem;
            color: #fff;
            background: linear-gradient(135deg, #ff4b53, #d80f27);
            box-shadow: 0 16px 28px rgba(216,15,39,.22);
        }

        .cashier-toolbar {
            display: flex;
            align-items: center;
            gap: .7rem;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-left: auto;
        }

        .cashier-toolbar .btn {
            min-height: 2.55rem;
            padding: .55rem 1rem;
        }

        .cashier-search {
            position: relative;
            flex: 1 1 220px;
            min-width: 220px;
            max-width: 320px;
        }

        .cashier-search i {
            position: absolute;
            top: 50%;
            left: .95rem;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .cashier-search input {
            width: 100%;
            min-height: 2.75rem;
            padding: .65rem 1rem .65rem 2.55rem;
            border: 1px solid var(--border);
            border-radius: 999px;
            outline: 0;
            color: var(--text-main);
            background: rgba(255,255,255,.055);
            font-weight: 700;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .cashier-search input::placeholder {
            color: var(--text-muted);
        }

        .cashier-search input:focus {
            border-color: rgba(255,66,74,.45);
            background: rgba(255,255,255,.075);
            box-shadow: 0 0 0 .25rem rgba(255,66,74,.12);
        }

        .cashier-panel-body {
            padding: 1rem 1.15rem 1.15rem;
        }

        .cashier-table-wrap {
            display: block;
            width: 100%;
            max-width: 100%;
            padding: .65rem .65rem 1rem;
            overflow-x: auto;
            overflow-y: hidden;
            overscroll-behavior-x: contain;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            cursor: grab;
            user-select: none;
            touch-action: pan-x;
        }

        .cashier-table-wrap::-webkit-scrollbar {
            display: none;
        }

        .cashier-table-wrap.is-dragging {
            cursor: grabbing;
        }

        .cashier-table-wrap.is-dragging * {
            cursor: grabbing !important;
        }

        .cashier-table {
            width: 980px;
            min-width: 980px;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0 .55rem;
        }

        .cashier-table th:nth-child(1),
        .cashier-table td:nth-child(1) {
            width: 76px;
        }

        .cashier-table th:nth-child(2),
        .cashier-table td:nth-child(2) {
            width: 180px;
        }

        .cashier-table th:nth-child(3),
        .cashier-table td:nth-child(3) {
            width: 215px;
        }

        .cashier-table th:nth-child(4),
        .cashier-table td:nth-child(4) {
            width: 150px;
        }

        .cashier-table th:nth-child(5),
        .cashier-table td:nth-child(5) {
            width: 145px;
        }

        .cashier-table th:nth-child(6),
        .cashier-table td:nth-child(6) {
            width: 116px;
        }

        .cashier-table thead th {
            padding: .7rem 1rem;
            color: var(--text-muted);
            border-bottom-color: var(--border);
            background: transparent;
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .cashier-table td {
            padding: .95rem 1rem;
            color: var(--text-main);
            border: 0;
            vertical-align: middle;
            background: rgba(255,255,255,.045);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.035), inset 0 -1px 0 rgba(0,0,0,.08);
        }

        .cashier-table td,
        .cashier-table th {
            white-space: nowrap;
        }

        .cashier-table td[data-label="Invoice"],
        .cashier-table td[data-label="Pelanggan"],
        .cashier-table td[data-label="Tipe"],
        .cashier-table td[data-label="Nominal"] {
            min-width: 0;
        }

        .cashier-table td:first-child {
            border-radius: .95rem 0 0 .95rem;
            border-left: 1px solid var(--border);
        }

        .cashier-table td:last-child {
            border-radius: 0 .95rem .95rem 0;
            border-right: 1px solid var(--border);
        }

        .cashier-table tbody tr:hover td {
            background: rgba(255,66,74,.075);
        }

        .cashier-table tbody tr {
            transition: transform .16s ease;
            cursor: pointer;
        }

        .cashier-table tbody tr:hover {
            transform: translateY(-1px);
        }

        .cashier-table tbody tr:focus-visible {
            outline: 2px solid rgba(255,66,74,.72);
            outline-offset: 3px;
        }

        .time-stack {
            display: inline-grid;
            gap: .1rem;
            min-width: 3.1rem;
        }

        .time-stack strong {
            line-height: 1;
        }

        .time-stack span {
            color: var(--text-muted);
            font-size: .72rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .invoice-chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-weight: 800;
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            overflow-wrap: normal;
            word-break: normal;
        }

        .invoice-chip-text {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .invoice-chip::before {
            content: '';
            width: .55rem;
            height: .55rem;
            border-radius: 999px;
            background: #ff424a;
            box-shadow: 0 0 0 .26rem rgba(255,66,74,.12);
        }

        .customer-cell {
            display: flex;
            align-items: center;
            gap: .72rem;
            min-width: 0;
            max-width: 100%;
        }

        .customer-avatar {
            width: 2.35rem;
            height: 2.35rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .8rem;
            color: #fff;
            background:
                linear-gradient(135deg, rgba(255,66,74,.92), rgba(120,24,32,.88)),
                rgba(255,255,255,.08);
            font-size: .82rem;
            font-weight: 800;
            flex: 0 0 auto;
        }

        .customer-name {
            display: block;
            font-weight: 800;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 126px;
        }

        .customer-sub {
            display: block;
            margin-top: .18rem;
            color: var(--text-muted);
            font-size: .78rem;
            font-weight: 700;
        }

        .type-pill,
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            min-height: 2rem;
            padding: .36rem .68rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .type-pill {
            color: #ffd5d8;
            background: rgba(255,66,74,.1);
            border: 1px solid rgba(255,66,74,.2);
        }

        .status-pill.is-paid {
            color: #d8ffe9;
            background: rgba(34,197,94,.14);
            border: 1px solid rgba(34,197,94,.28);
        }

        .status-pill.is-pending {
            color: #fff4c7;
            background: rgba(234,179,8,.16);
            border: 1px solid rgba(234,179,8,.28);
        }

        .amount-text {
            display: block;
            color: var(--text-main);
            font-size: .98rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .amount-subtext {
            display: none;
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .table-scroll-hint {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: .15rem .75rem .7rem;
            color: var(--text-muted);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .cashier-side {
            display: grid;
            gap: 1rem;
        }

        .cashier-side-list {
            display: grid;
            gap: .75rem;
        }

        .cashier-side-item {
            padding: .9rem;
            border: 1px solid var(--border);
            border-radius: .95rem;
            background:
                linear-gradient(135deg, rgba(255,255,255,.055), rgba(255,255,255,.02)),
                rgba(255,255,255,.025);
            transition: transform .16s ease, border-color .16s ease;
        }

        .cashier-side-item:hover {
            transform: translateX(2px);
            border-color: rgba(255,66,74,.24);
        }

        .cashier-side-title {
            color: var(--text-main);
            font-weight: 800;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 190px;
        }

        .side-item-row {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: .75rem;
            align-items: start;
            min-width: 0;
        }

        .receipt-icon {
            width: 2.2rem;
            height: 2.2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            color: #ffd7da;
            background: rgba(255,66,74,.12);
            border: 1px solid rgba(255,66,74,.18);
        }

        .receipt-actions {
            display: grid;
            gap: .45rem;
            justify-items: end;
        }

        .receipt-check-btn {
            min-width: 4.2rem;
            border-radius: 999px;
            border: 1px solid rgba(255,205,61,.34);
            color: #fff4c7;
            background: rgba(234,179,8,.16);
            font-size: .78rem;
            font-weight: 800;
            transition: transform .16s ease, filter .16s ease;
        }

        .receipt-check-btn:hover,
        .receipt-check-btn:focus {
            color: #fff;
            filter: brightness(1.14);
            transform: translateY(-1px);
        }

        .receipt-print-btn {
            min-width: 4.2rem;
            border-radius: 999px;
            color: #d8ffe9;
            border: 1px solid rgba(34,197,94,.28);
            background: rgba(34,197,94,.14);
            font-size: .78rem;
            font-weight: 800;
            text-decoration: none;
            text-align: center;
            padding: .38rem .7rem;
        }

        .receipt-print-btn:hover,
        .receipt-print-btn:focus {
            color: #fff;
            filter: brightness(1.12);
        }

        .receipt-detail-btn {
            min-width: 6.8rem;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.16);
            color: #fff;
            background: rgba(255,255,255,.08);
            font-size: .78rem;
            font-weight: 800;
            transition: transform .16s ease, background .16s ease, border-color .16s ease;
        }

        .receipt-detail-btn:hover,
        .receipt-detail-btn:focus {
            color: #fff;
            transform: translateY(-1px);
            border-color: rgba(255,66,74,.32);
            background: rgba(255,66,74,.16);
        }

        .transaction-actions {
            display: flex;
            gap: .7rem;
            flex-wrap: wrap;
            padding: 0 1.15rem 1.15rem;
        }

        .transaction-actions .btn,
        .transaction-actions a {
            min-height: 2.65rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-weight: 800;
            text-decoration: none;
        }

        .cashier-empty {
            padding: 1.1rem;
            border: 1px dashed var(--border);
            border-radius: .95rem;
            color: var(--text-muted);
            text-align: center;
        }

        .method-row + .method-row {
            margin-top: 1rem;
        }

        .method-row {
            padding: .85rem;
            border: 1px solid var(--border);
            border-radius: .95rem;
            background: rgba(255,255,255,.035);
        }

        .dashboard-toast {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 2100;
            max-width: min(360px, calc(100vw - 2rem));
            padding: .9rem 1rem;
            border: 1px solid rgba(255,66,74,.28);
            border-radius: 1rem;
            color: #fff;
            background: rgba(15,15,18,.88);
            box-shadow: 0 24px 50px rgba(0,0,0,.42);
            backdrop-filter: blur(18px);
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
            transition: opacity .2s ease, transform .2s ease;
        }

        .dashboard-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .history-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 2210;
            width: min(460px, 100vw);
            display: flex;
            flex-direction: column;
            border-left: 1px solid rgba(255,255,255,.12);
            background:
                radial-gradient(circle at top right, rgba(255,66,74,.18), transparent 18rem),
                rgba(15,15,18,.96);
            box-shadow: -28px 0 70px rgba(0,0,0,.48);
            backdrop-filter: blur(22px);
            transform: translateX(105%);
            transition: transform .24s ease;
        }

        .history-sidebar.show {
            transform: translateX(0);
        }

        .history-overlay {
            position: fixed;
            inset: 0;
            z-index: 2205;
            display: none;
            background: rgba(0,0,0,.48);
            backdrop-filter: blur(5px);
        }

        .history-overlay.show {
            display: block;
        }

        .history-head {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-start;
            padding: 1.1rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        .history-close {
            width: 2.45rem;
            height: 2.45rem;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: .85rem;
            color: #fff;
            background: rgba(255,255,255,.06);
        }

        .history-tabs {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .55rem;
            padding: .85rem 1.1rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .history-tab {
            min-height: 2.55rem;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 999px;
            color: var(--text-muted);
            background: rgba(255,255,255,.045);
            font-size: .82rem;
            font-weight: 800;
        }

        .history-tab.active {
            color: #fff;
            border-color: rgba(255,66,74,.38);
            background: rgba(255,66,74,.18);
        }

        .history-body {
            flex: 1;
            overflow: auto;
            padding: 1rem 1.1rem 1.2rem;
        }

        .history-section {
            display: none;
        }

        .history-section.active {
            display: grid;
            gap: .75rem;
        }

        .history-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: .8rem;
            padding: .9rem;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 1rem;
            background: rgba(255,255,255,.045);
        }

        .history-icon {
            width: 2.45rem;
            height: 2.45rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .85rem;
            color: #ffd5d8;
            background: rgba(255,66,74,.13);
            border: 1px solid rgba(255,66,74,.2);
        }

        .history-title {
            color: #fff;
            font-weight: 800;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .history-meta {
            margin-top: .25rem;
            color: var(--text-muted);
            font-size: .82rem;
            line-height: 1.45;
        }

        .history-foot {
            display: flex;
            justify-content: space-between;
            gap: .75rem;
            align-items: center;
            margin-top: .65rem;
            flex-wrap: wrap;
        }

        .history-amount {
            color: #fff;
            font-weight: 800;
        }

        .transaction-modal {
            position: fixed;
            inset: 0;
            z-index: 2200;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(0,0,0,.54);
            backdrop-filter: blur(8px);
        }

        .transaction-modal.show {
            display: flex;
        }

        .transaction-dialog {
            width: min(760px, 100%);
            max-height: calc(100vh - 2rem);
            overflow: auto;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 1.25rem;
            background:
                radial-gradient(circle at top right, rgba(255,66,74,.2), transparent 18rem),
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)),
                rgba(18,18,20,.96);
            box-shadow: 0 28px 70px rgba(0,0,0,.52);
            scrollbar-width: thin;
            scrollbar-color: rgba(255,66,74,.45) transparent;
        }

        .transaction-dialog-head {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-start;
            padding: 1.35rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        .transaction-dialog-title {
            display: flex;
            align-items: center;
            gap: .9rem;
        }

        .transaction-dialog-icon {
            width: 3.1rem;
            height: 3.1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            color: #fff;
            background: linear-gradient(135deg, #ff4b53, #d80f27);
            box-shadow: 0 18px 34px rgba(216,15,39,.28);
            flex: 0 0 auto;
        }

        .transaction-close {
            width: 2.4rem;
            height: 2.4rem;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: .8rem;
            color: #fff;
            background: rgba(255,255,255,.06);
        }

        .transaction-summary {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1rem;
            align-items: center;
            margin: 1.15rem 1.25rem 0;
            padding: 1.1rem;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 1rem;
            background:
                radial-gradient(circle at top left, rgba(255,66,74,.2), transparent 18rem),
                linear-gradient(135deg, rgba(255,66,74,.14), rgba(255,255,255,.04));
        }

        .transaction-summary-label {
            color: var(--text-muted);
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .transaction-summary-amount {
            display: block;
            margin-top: .25rem;
            color: #fff;
            font-size: clamp(1.5rem, 3vw, 2.15rem);
            line-height: 1;
            font-weight: 800;
        }

        .transaction-summary-status {
            display: grid;
            gap: .45rem;
            justify-items: end;
        }

        .transaction-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
            padding: 1.15rem 1.25rem 1.25rem;
        }

        .transaction-detail-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: .78rem;
            align-items: center;
            padding: .9rem;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1rem;
            background: linear-gradient(135deg, rgba(255,255,255,.055), rgba(255,255,255,.02));
        }

        .transaction-detail-item.wide {
            grid-column: 1 / -1;
        }

        .transaction-detail-icon {
            width: 2.45rem;
            height: 2.45rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .85rem;
            color: #ffd5d8;
            background: rgba(255,66,74,.12);
            border: 1px solid rgba(255,66,74,.18);
        }

        .transaction-detail-copy {
            min-width: 0;
            display: grid;
            gap: .18rem;
        }

        .transaction-detail-copy span {
            color: var(--text-muted);
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .transaction-detail-copy strong {
            color: #fff;
            overflow-wrap: anywhere;
            font-size: .98rem;
            line-height: 1.4;
        }

        @media (max-width: 1199.98px) {
            .cashier-hero,
            .cashier-grid {
                grid-template-columns: 1fr;
            }

            .cashier-hero-title {
                max-width: 16ch;
            }

            .cashier-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            body.page-cashier-dashboard main.col-12 {
                padding: .75rem;
            }

            .cashier-page {
                min-height: calc(100vh - 1.5rem);
            }

            .cashier-hero {
                min-height: auto;
            }

            .cashier-hero-main,
            .cashier-hero-side {
                padding: 1rem;
            }

            .cashier-actions,
            .cashier-stats {
                grid-template-columns: 1fr;
            }

            .cashier-action {
                min-height: 64px;
            }

            .cashier-panel-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .cashier-panel-header .btn {
                width: auto;
            }

            .cashier-toolbar {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                margin-left: 0;
            }

            .cashier-toolbar .btn {
                width: 100%;
            }

            .cashier-search {
                width: 100%;
                max-width: none;
                min-width: 0;
                grid-column: 1 / -1;
            }

            .cashier-table-wrap {
                padding: .75rem;
                overflow-x: auto;
                overflow-y: hidden;
            }

            .cashier-table {
                min-width: 920px;
                width: 920px;
                table-layout: fixed;
                border-spacing: 0 .55rem;
            }

            .cashier-table thead {
                display: table-header-group;
            }

            .cashier-table tbody,
            .cashier-table tr,
            .cashier-table td {
                display: revert;
                width: auto;
            }

            .cashier-table tbody {
                display: table-row-group;
            }

            .cashier-table tbody tr {
                display: table-row;
                gap: 0;
                padding: 0;
                border: 0;
                border-radius: 0;
                background: transparent;
            }

            .cashier-table tbody tr:hover {
                transform: none;
            }

            .cashier-table td {
                padding: .95rem 1rem;
                border: 0;
                background: rgba(255,255,255,.045) !important;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.035), inset 0 -1px 0 rgba(0,0,0,.08);
                white-space: nowrap;
            }

            .cashier-table td[data-label] {
                display: table-cell;
            }

            .cashier-table td[data-label]::before {
                content: none;
            }

            .invoice-chip {
                min-width: 0;
                max-width: 150px;
                white-space: nowrap;
                overflow-wrap: normal;
            }

            .invoice-chip-text {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .customer-cell {
                min-width: 0;
                max-width: 100%;
            }

            .customer-name {
                max-width: 100%;
            }

            .type-pill,
            .status-pill,
            .amount-text {
                max-width: 100%;
                white-space: nowrap;
            }

            .side-item-row {
                grid-template-columns: auto minmax(0, 1fr);
            }

            .receipt-actions {
                grid-column: 1 / -1;
                justify-items: stretch;
            }

            .receipt-check-btn,
            .receipt-print-btn {
                width: 100%;
            }

            .transaction-detail-item {
                grid-template-columns: 1fr;
                gap: .25rem;
            }

            .transaction-detail-grid,
            .transaction-summary {
                grid-template-columns: 1fr;
            }

            .transaction-summary-status {
                justify-items: start;
            }
        }
    </style>

    <div class="cashier-page">
        @php
            $dashboardTransactions = $transactions
                ->filter(fn ($transaction) => $transaction->transaction_at?->isToday())
                ->take(5)
                ->values();
            $dashboardReceiptQueue = $receiptQueue
                ->filter(fn ($receipt) => $receipt->transaction_at?->isToday())
                ->filter(fn ($receipt) => $receipt->payment_method === 'qris')
                ->filter(fn ($receipt) => $receipt->payment_status !== 'verified')
                ->take(4)
                ->values();
            $historyMemberTransactions = $transactions
                ->filter(fn ($transaction) => $transaction->transaction_group === 'member_payment')
                ->values();
            $historyDailyPassTransactions = $transactions
                ->filter(fn ($transaction) => $transaction->transaction_group === 'daily_pass')
                ->values();
            $historyProductTransactions = $transactions
                ->filter(fn ($transaction) => $transaction->transaction_group === 'product_sale' || $transaction->product_id)
                ->values();
        @endphp

        <section class="cashier-hero" data-hero-spotlight>
            <div class="cashier-hero-main">
                <div>
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" class="brand-logo-inline">
                            <div>
                                <div class="cashier-kicker">Arena Fitness Cashier</div>
                            </div>
                        </div>
                        <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema">
                            <span class="theme-toggle-track" aria-hidden="true">
                                <span class="theme-toggle-thumb"></span>
                            </span>
                        </button>
                    </div>
                </div>

                <div class="cashier-actions">
                    <a href="{{ route('cashier.transactions') }}" class="cashier-action primary">
                        <span class="cashier-action-icon"><i class="fas fa-credit-card"></i></span>
                        <span>Transaksi</span>
                    </a>
                    <a href="{{ route('cashier.checkins') }}" class="cashier-action">
                        <span class="cashier-action-icon"><i class="fas fa-person-walking"></i></span>
                        <span>Check-in</span>
                    </a>
                    <a href="{{ route('cashier.transactions.products') }}" class="cashier-action">
                        <span class="cashier-action-icon"><i class="fas fa-basket-shopping"></i></span>
                        <span>Checkout Barang</span>
                    </a>
                </div>
            </div>

            <div class="cashier-hero-side">
                <div class="shift-card"
                    data-shift-start="{{ $cashierShift['start'] ?? '08:00' }}"
                    data-shift-end="{{ $cashierShift['end'] ?? '16:00' }}">
                    <div class="shift-top">
                        <div class="section-label text-white-50">Shift Aktif</div>
                        <span class="shift-status">Online</span>
                    </div>
                    <div class="shift-time">{{ $cashierShift['label'] ?? '08:00 - 16:00' }}</div>
                    <div class="shift-countdown js-shift-countdown">Menghitung waktu shift...</div>
                </div>

                <div class="cashier-image-card">
                    <div>
                        <strong>Operasional hari ini</strong>
                        <span>Transaksi masuk, bukti dicek, dan laporan tetap rapi.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="cashier-stats" aria-label="Ringkasan dashboard kasir">
            @foreach ($cashierStats as $index => $stat)
                @php
                    $icons = ['fa-credit-card', 'fa-ticket', 'fa-receipt', 'fa-chart-line'];
                    $icon = $icons[$index] ?? 'fa-circle-info';
                @endphp
                <div class="cashier-stat">
                    <div class="cashier-stat-head">
                        <div class="cashier-stat-label">{{ $stat['label'] }}</div>
                        <span class="cashier-stat-icon"><i class="fas {{ $icon }}"></i></span>
                    </div>
                    <div class="cashier-stat-value">{{ $stat['value'] }}</div>
                    <span class="cashier-stat-note">{{ $stat['change'] }}</span>
                </div>
            @endforeach
        </section>

        <section class="cashier-grid">
            <div class="cashier-panel elevated">
                <div class="cashier-panel-header">
                    <div class="cashier-panel-title">
                        <span class="cashier-panel-title-icon"><i class="fas fa-receipt"></i></span>
                        <div>
                            <div class="section-label">Riwayat Hari Ini</div>
                            <h2 class="h5 fw-bold mt-2 mb-0">Transaksi Terbaru</h2>
                        </div>
                    </div>
                    <div class="cashier-toolbar">
                        <label class="cashier-search" for="transactionSearch">
                            <i class="fas fa-search"></i>
                            <input id="transactionSearch" type="search" placeholder="Cari transaksi..." data-transaction-search>
                        </label>
                        <a href="{{ route('cashier.transactions') }}" class="btn btn-dark rounded-pill px-4 fw-semibold">Lihat Semua</a>
                    </div>
                </div>
                <div class="table-responsive cashier-table-wrap" data-table-scroll-main>
                    <table class="table cashier-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Invoice</th>
                                <th>Pelanggan</th>
                                <th>Tipe</th>
                                <th>Nominal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dashboardTransactions as $item)
                                @php
                                    $customerName = $item->customer_name ?? $item->member?->full_name ?? 'Tidak dikenal';
                                    $transactionType = ucfirst(str_replace('_', ' ', $item->transaction_group ?? $item->transaction_type ?? '-'));
                                    $initials = collect(explode(' ', trim($customerName)))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                                        ->implode('') ?: 'AF';
                                    $isPaid = $item->payment_status === 'verified';
                                    $paymentMethod = $item->payment_method === 'later' ? 'Bayar Nanti' : strtoupper((string) $item->payment_method);
                                    $receiptLabel = $item->receipt_status === 'printed'
                                        ? 'Sudah Dicetak'
                                        : ($item->receipt_status === 'ready' ? 'Siap Cetak' : 'Menunggu Verifikasi');
                                    $transactionDetail = [
                                        'invoice' => $item->invoice,
                                        'customer' => $customerName,
                                        'type' => $transactionType,
                                        'amount' => 'Rp' . number_format($item->amount, 0, ',', '.'),
                                        'paidAmount' => 'Rp' . number_format($item->paid_amount ?? $item->amount, 0, ',', '.'),
                                        'changeAmount' => 'Rp' . number_format($item->change_amount ?? 0, 0, ',', '.'),
                                        'paymentMethod' => $paymentMethod,
                                        'paymentStatus' => $isPaid ? 'Lunas' : 'Pending',
                                        'receiptStatus' => $receiptLabel,
                                        'time' => $item->transaction_at?->format('H:i, d M Y') ?? '-',
                                        'quantity' => (string) ($item->quantity ?? 1),
                                        'notes' => $item->notes ?: '-',
                                    ];
                                @endphp
                                <tr
                                    role="button"
                                    tabindex="0"
                                    aria-label="Lihat detail transaksi {{ $item->invoice }}"
                                    data-transaction-row
                                    data-search="{{ strtolower($item->invoice . ' ' . $customerName . ' ' . $transactionType . ' ' . $item->amount) }}"
                                    data-detail='@json($transactionDetail)'
                                >
                                    <td data-label="Waktu">
                                        <span class="time-stack">
                                            <strong>{{ $item->transaction_at?->format('H:i') ?? '-' }}</strong>
                                            <span>Hari ini</span>
                                        </span>
                                    </td>
                                    <td data-label="Invoice"><span class="invoice-chip"><span class="invoice-chip-text">{{ $item->invoice }}</span></span></td>
                                    <td data-label="Pelanggan">
                                        <div class="customer-cell">
                                            <span class="customer-avatar">{{ $initials }}</span>
                                            <span>
                                                <span class="customer-name">{{ $customerName }}</span>
                                                <span class="customer-sub">Arena Fitness</span>
                                            </span>
                                        </div>
                                    </td>
                                    <td data-label="Tipe"><span class="type-pill"><i class="fas fa-layer-group"></i>{{ $transactionType }}</span></td>
                                    <td data-label="Nominal">
                                        <span class="amount-text">Rp{{ number_format($item->amount, 0, ',', '.') }}</span>
                                        <span class="small muted-copy amount-subtext">Terima Rp{{ number_format($item->paid_amount ?? $item->amount, 0, ',', '.') }} • Kembali Rp{{ number_format($item->change_amount ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                    <td data-label="Status">
                                        <span class="status-pill {{ $isPaid ? 'is-paid' : 'is-pending' }}">
                                            <i class="fas {{ $isPaid ? 'fa-check' : 'fa-clock' }}"></i>
                                            {{ $isPaid ? 'Lunas' : 'Pending' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4">
                                        <div class="cashier-empty">Belum ada transaksi hari ini.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="table-scroll-hint">
                        <span>5 transaksi terbaru</span>
                        <span>Geser kanan/kiri</span>
                    </div>
                    <div class="cashier-empty d-none m-3" data-transaction-empty>Transaksi tidak ditemukan.</div>
                </div>
            </div>

            <div class="cashier-side">
                <div class="cashier-panel elevated">
                    <div class="cashier-panel-header">
                        <div class="cashier-panel-title">
                            <span class="cashier-panel-title-icon"><i class="fas fa-file-invoice"></i></span>
                            <div>
                                <div class="section-label">Perlu Dicek</div>
                                <h2 class="h5 fw-bold mt-2 mb-0">Bukti Pembayaran</h2>
                            </div>
                        </div>
                        <a href="{{ route('cashier.receipts') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">Buka</a>
                    </div>
                    <div class="cashier-panel-body">
                        <div class="cashier-side-list">
                            @forelse ($dashboardReceiptQueue as $item)
                                @php
                                    $receiptCustomer = $item->customer_name ?? $item->member?->full_name ?? 'Tidak dikenal';
                                    $receiptType = ucfirst(str_replace('_', ' ', $item->transaction_group ?? $item->transaction_type ?? '-'));
                                    $receiptStatusText = $item->receipt_status === 'printed'
                                        ? 'Sudah Dicetak'
                                        : ($item->receipt_status === 'ready' ? 'Siap Cetak' : 'Menunggu Verifikasi');
                                    $receiptDetail = [
                                        'invoice' => $item->invoice,
                                        'customer' => $receiptCustomer,
                                        'type' => $receiptType,
                                        'amount' => 'Rp' . number_format($item->amount, 0, ',', '.'),
                                        'paidAmount' => 'Rp' . number_format($item->paid_amount ?? $item->amount, 0, ',', '.'),
                                        'changeAmount' => 'Rp' . number_format($item->change_amount ?? 0, 0, ',', '.'),
                                        'paymentMethod' => $item->payment_method === 'later' ? 'Bayar Nanti' : strtoupper((string) $item->payment_method),
                                        'paymentStatus' => $item->payment_status === 'verified' ? 'Lunas' : 'Pending',
                                        'receiptStatus' => $receiptStatusText,
                                        'time' => $item->transaction_at?->format('H:i, d M Y') ?? '-',
                                        'printUrl' => route('cashier.receipts.print', $item->invoice),
                                        'verifyUrl' => route('cashier.verifications.confirm', $item->id),
                                        'canPrint' => $item->payment_status === 'verified',
                                        'canFinish' => $item->payment_status !== 'verified',
                                    ];
                                @endphp
                                <div class="cashier-side-item">
                                    <div class="side-item-row">
                                        <span class="receipt-icon"><i class="fas fa-file-lines"></i></span>
                                        <div>
                                            <div class="cashier-side-title">{{ $item->invoice }}</div>
                                            <div class="small muted-copy mt-1">
                                                {{ $receiptCustomer }}
                                                - {{ $receiptType }}
                                            </div>
                                        </div>
                                        <div class="receipt-actions">
                                            <button class="receipt-detail-btn btn btn-sm" type="button" data-receipt-detail='@json($receiptDetail)'>
                                                Lihat Detail
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="cashier-empty">Tidak ada QRIS terbaru yang perlu diverifikasi.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="cashier-panel elevated">
                    <div class="cashier-panel-header">
                        <div class="cashier-panel-title">
                            <span class="cashier-panel-title-icon"><i class="fas fa-wallet"></i></span>
                            <div>
                                <div class="section-label">Metode Bayar</div>
                                <h2 class="h5 fw-bold mt-2 mb-0">Ringkasan</h2>
                            </div>
                        </div>
                    </div>
                    <div class="cashier-panel-body">
                        @forelse ($paymentMethods as $item)
                            <div class="method-row">
                                <div class="d-flex justify-content-between small mb-2">
                                    <span class="muted-copy fw-semibold">{{ $item['label'] }}</span>
                                    <span class="fw-bold">{{ $item['value'] }}</span>
                                </div>
                                <div class="mini-progress">
                                    <div class="mini-progress-bar bg-{{ $item['color'] }}" style="width: {{ $item['progress'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="cashier-empty">Belum ada pembayaran lunas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="dashboard-toast" data-dashboard-toast>Filter transaksi aktif.</div>

    <div class="history-overlay" data-history-overlay></div>
    <aside class="history-sidebar" data-history-sidebar aria-hidden="true" aria-label="Riwayat transaksi">
        <div class="history-head">
            <div>
                <div class="section-label">Riwayat Transaksi</div>
                <h2 class="h5 fw-bold mt-2 mb-1">Semua Pembayaran</h2>
                <p class="muted-copy mb-0">Dipisahkan berdasarkan member, daily pass, dan produk.</p>
            </div>
            <button class="history-close" type="button" data-history-close aria-label="Tutup riwayat transaksi">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="history-tabs" role="tablist" aria-label="Kategori riwayat transaksi">
            <button class="history-tab active" type="button" data-history-tab="member">Member</button>
            <button class="history-tab" type="button" data-history-tab="daily-pass">Daily Pass</button>
            <button class="history-tab" type="button" data-history-tab="produk">Produk</button>
        </div>

        <div class="history-body">
            @foreach ([
                'member' => ['items' => $historyMemberTransactions, 'icon' => 'fa-id-card', 'empty' => 'Belum ada riwayat pembayaran member.'],
                'daily-pass' => ['items' => $historyDailyPassTransactions, 'icon' => 'fa-person-walking', 'empty' => 'Belum ada riwayat daily pass.'],
                'produk' => ['items' => $historyProductTransactions, 'icon' => 'fa-basket-shopping', 'empty' => 'Belum ada riwayat produk.'],
            ] as $historyKey => $history)
                <div class="history-section {{ $historyKey === 'member' ? 'active' : '' }}" data-history-section="{{ $historyKey }}">
                    @forelse ($history['items'] as $historyItem)
                        @php
                            $historyCustomer = $historyItem->customer_name ?? $historyItem->member?->full_name ?? 'Tidak dikenal';
                            $historyType = ucfirst(str_replace('_', ' ', $historyItem->transaction_group ?? $historyItem->transaction_type ?? '-'));
                            $historyStatus = $historyItem->payment_status === 'verified' ? 'Lunas' : 'Pending';
                            $historyStatusClass = $historyItem->payment_status === 'verified' ? 'is-paid' : 'is-pending';
                        @endphp
                        <div class="history-item">
                            <span class="history-icon"><i class="fas {{ $history['icon'] }}"></i></span>
                            <div>
                                <div class="history-title">{{ $historyItem->invoice }}</div>
                                <div class="history-meta">
                                    {{ $historyCustomer }} - {{ $historyType }}<br>
                                    {{ $historyItem->transaction_at?->format('d M Y H:i') ?? '-' }} - {{ strtoupper((string) $historyItem->payment_method) }}
                                </div>
                                <div class="history-foot">
                                    <span class="history-amount">Rp{{ number_format($historyItem->amount, 0, ',', '.') }}</span>
                                    <span class="small muted-copy">Terima Rp{{ number_format($historyItem->paid_amount ?? $historyItem->amount, 0, ',', '.') }} • Kembali Rp{{ number_format($historyItem->change_amount ?? 0, 0, ',', '.') }}</span>
                                    <span class="status-pill {{ $historyStatusClass }}">{{ $historyStatus }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="cashier-empty">{{ $history['empty'] }}</div>
                    @endforelse
                </div>
            @endforeach
        </div>
    </aside>

    <div class="transaction-modal" data-transaction-modal aria-hidden="true">
        <div class="transaction-dialog" role="dialog" aria-modal="true" aria-labelledby="transactionModalTitle">
            <div class="transaction-dialog-head">
                <div class="transaction-dialog-title">
                    <span class="transaction-dialog-icon"><i class="fas fa-receipt"></i></span>
                    <div>
                        <div class="section-label">Detail Transaksi</div>
                        <h2 class="h5 fw-bold mt-2 mb-0" id="transactionModalTitle" data-detail-title>Invoice</h2>
                    </div>
                </div>
                <button class="transaction-close" type="button" data-transaction-close aria-label="Tutup detail transaksi">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="transaction-summary">
                <div>
                    <span class="transaction-summary-label">Total Transaksi</span>
                    <strong class="transaction-summary-amount" data-detail-amount>-</strong>
                </div>
                <div class="transaction-summary-status">
                    <span class="status-pill is-pending" data-detail-payment-status>-</span>
                    <span class="status-pill is-pending" data-detail-receipt-status>-</span>
                </div>
            </div>
            <div class="transaction-detail-grid">
                <div class="transaction-detail-item">
                    <span class="transaction-detail-icon"><i class="fas fa-user"></i></span>
                    <div class="transaction-detail-copy"><span>Pelanggan</span><strong data-detail-customer>-</strong></div>
                </div>
                <div class="transaction-detail-item">
                    <span class="transaction-detail-icon"><i class="fas fa-layer-group"></i></span>
                    <div class="transaction-detail-copy"><span>Tipe</span><strong data-detail-type>-</strong></div>
                </div>
                <div class="transaction-detail-item">
                    <span class="transaction-detail-icon"><i class="fas fa-wallet"></i></span>
                    <div class="transaction-detail-copy"><span>Metode Bayar</span><strong data-detail-payment-method>-</strong></div>
                </div>
                <div class="transaction-detail-item">
                    <span class="transaction-detail-icon"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="transaction-detail-copy"><span>Uang Diterima</span><strong data-detail-paid-amount>-</strong></div>
                </div>
                <div class="transaction-detail-item">
                    <span class="transaction-detail-icon"><i class="fas fa-coins"></i></span>
                    <div class="transaction-detail-copy"><span>Kembalian</span><strong data-detail-change-amount>-</strong></div>
                </div>
                <div class="transaction-detail-item">
                    <span class="transaction-detail-icon"><i class="fas fa-clock"></i></span>
                    <div class="transaction-detail-copy"><span>Waktu</span><strong data-detail-time>-</strong></div>
                </div>
                <div class="transaction-detail-item">
                    <span class="transaction-detail-icon"><i class="fas fa-hashtag"></i></span>
                    <div class="transaction-detail-copy"><span>Jumlah</span><strong data-detail-quantity>-</strong></div>
                </div>
                <div class="transaction-detail-item wide">
                    <span class="transaction-detail-icon"><i class="fas fa-note-sticky"></i></span>
                    <div class="transaction-detail-copy"><span>Catatan</span><strong data-detail-notes>-</strong></div>
                </div>
            </div>
            <div class="transaction-actions" data-detail-actions>
                <a class="btn btn-success px-4 d-none" href="#" target="_blank" data-detail-print>
                    <i class="fas fa-print me-2"></i>Cetak
                </a>
                <form method="POST" action="#" class="d-none" data-detail-finish-form onsubmit="return confirm('Selesaikan dan verifikasi pembayaran ini?')">
                    @csrf
                    <input type="hidden" name="return_to" value="dashboard">
                    <button class="btn btn-dark px-4" type="submit">
                        <i class="fas fa-check me-2"></i>Selesai
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const shiftCard = document.querySelector('[data-shift-start][data-shift-end]');

            if (shiftCard) {
                const startValue = shiftCard.dataset.shiftStart;
                const endValue = shiftCard.dataset.shiftEnd;
                const countdownEl = shiftCard.querySelector('.js-shift-countdown');

                const toDate = (timeValue) => {
                    const [hours, minutes] = timeValue.split(':').map(Number);
                    const now = new Date();
                    return new Date(now.getFullYear(), now.getMonth(), now.getDate(), hours, minutes, 0, 0);
                };

                const formatRemaining = (milliseconds) => {
                    const totalSeconds = Math.max(Math.floor(milliseconds / 1000), 0);
                    const totalMinutes = Math.floor(totalSeconds / 60);
                    const hours = Math.floor(totalMinutes / 60);
                    const minutes = totalMinutes % 60;
                    const seconds = totalSeconds % 60;
                    const secondLabel = `${String(seconds).padStart(2, '0')} detik`;

                    return hours <= 0
                        ? `${minutes} menit ${secondLabel}`
                        : `${hours} jam ${minutes} menit ${secondLabel}`;
                };

                const updateShiftCountdown = () => {
                    const now = new Date();
                    const startAt = toDate(startValue);
                    const endAt = toDate(endValue);

                    if (now < startAt) {
                        countdownEl.textContent = `Shift dimulai dalam ${formatRemaining(startAt - now)}`;
                        return;
                    }

                    if (now >= endAt) {
                        countdownEl.textContent = 'Shift hari ini sudah selesai';
                        return;
                    }

                    countdownEl.textContent = `Sisa waktu shift ${formatRemaining(endAt - now)}`;
                };

                updateShiftCountdown();
                window.setInterval(updateShiftCountdown, 1000);
            }

            const heroCard = document.querySelector('[data-hero-spotlight]');

            if (heroCard && window.matchMedia('(pointer: fine)').matches) {
                heroCard.addEventListener('pointermove', (event) => {
                    const rect = heroCard.getBoundingClientRect();
                    const x = ((event.clientX - rect.left) / rect.width) * 100;
                    const y = ((event.clientY - rect.top) / rect.height) * 100;
                    heroCard.style.setProperty('--hero-x', `${x}%`);
                    heroCard.style.setProperty('--hero-y', `${y}%`);
                });
            }

            const searchInput = document.querySelector('[data-transaction-search]');
            const rows = [...document.querySelectorAll('[data-transaction-row]')];
            const emptyState = document.querySelector('[data-transaction-empty]');
            const tableScrollMain = document.querySelector('[data-table-scroll-main]');
            const toast = document.querySelector('[data-dashboard-toast]');
            let toastTimeout;
            let isPointerDown = false;
            let dragStartX = 0;
            let dragStartScrollLeft = 0;

            tableScrollMain?.addEventListener('pointerdown', (event) => {
                if (event.pointerType === 'mouse' && event.button !== 0) {
                    return;
                }
                isPointerDown = true;
                dragStartX = event.clientX;
                dragStartScrollLeft = tableScrollMain.scrollLeft;
                tableScrollMain.classList.add('is-dragging');
                tableScrollMain.setPointerCapture?.(event.pointerId);
            });

            tableScrollMain?.addEventListener('pointermove', (event) => {
                if (!isPointerDown) {
                    return;
                }
                const deltaX = event.clientX - dragStartX;
                tableScrollMain.scrollLeft = dragStartScrollLeft - deltaX;
            });

            const stopDragScroll = (event) => {
                isPointerDown = false;
                tableScrollMain?.classList.remove('is-dragging');
                if (event && tableScrollMain && event.pointerId !== undefined) {
                    tableScrollMain.releasePointerCapture?.(event.pointerId);
                }
            };

            tableScrollMain?.addEventListener('pointerup', stopDragScroll);
            tableScrollMain?.addEventListener('pointercancel', stopDragScroll);
            tableScrollMain?.addEventListener('lostpointercapture', () => {
                isPointerDown = false;
                tableScrollMain?.classList.remove('is-dragging');
            });

            const showToast = (message) => {
                if (!toast) {
                    return;
                }

                toast.textContent = message;
                toast.classList.add('show');
                window.clearTimeout(toastTimeout);
                toastTimeout = window.setTimeout(() => toast.classList.remove('show'), 1800);
            };

            searchInput?.addEventListener('input', () => {
                const keyword = searchInput.value.trim().toLowerCase();
                let visibleRows = 0;

                rows.forEach((row) => {
                    const isMatch = !keyword || row.dataset.search.includes(keyword);
                    row.classList.toggle('d-none', !isMatch);
                    visibleRows += isMatch ? 1 : 0;
                });

                emptyState?.classList.toggle('d-none', visibleRows > 0);

                if (keyword) {
                    showToast(`${visibleRows} transaksi cocok dengan pencarian.`);
                }
            });

            const modal = document.querySelector('[data-transaction-modal]');
            const closeModalButton = document.querySelector('[data-transaction-close]');
            const detailFields = {
                title: document.querySelector('[data-detail-title]'),
                customer: document.querySelector('[data-detail-customer]'),
                type: document.querySelector('[data-detail-type]'),
                amount: document.querySelector('[data-detail-amount]'),
                paidAmount: document.querySelector('[data-detail-paid-amount]'),
                changeAmount: document.querySelector('[data-detail-change-amount]'),
                paymentMethod: document.querySelector('[data-detail-payment-method]'),
                paymentStatus: document.querySelector('[data-detail-payment-status]'),
                receiptStatus: document.querySelector('[data-detail-receipt-status]'),
                time: document.querySelector('[data-detail-time]'),
                quantity: document.querySelector('[data-detail-quantity]'),
                notes: document.querySelector('[data-detail-notes]'),
            };
            const detailPrint = document.querySelector('[data-detail-print]');
            const detailFinishForm = document.querySelector('[data-detail-finish-form]');

            const closeModal = () => {
                modal?.classList.remove('show');
                modal?.setAttribute('aria-hidden', 'true');
            };

            const openModal = (detail) => {
                if (!modal) {
                    return;
                }

                detailFields.title.textContent = detail.invoice || 'Detail Transaksi';
                detailFields.customer.textContent = detail.customer || '-';
                detailFields.type.textContent = detail.type || '-';
                detailFields.amount.textContent = detail.amount || '-';
                detailFields.paidAmount.textContent = detail.paidAmount || '-';
                detailFields.changeAmount.textContent = detail.changeAmount || '-';
                detailFields.paymentMethod.textContent = detail.paymentMethod || '-';
                detailFields.paymentStatus.textContent = detail.paymentStatus || '-';
                detailFields.receiptStatus.textContent = detail.receiptStatus || '-';
                detailFields.paymentStatus.className = `status-pill ${detail.paymentStatus === 'Lunas' ? 'is-paid' : 'is-pending'}`;
                detailFields.receiptStatus.className = `status-pill ${detail.receiptStatus === 'Sudah Dicetak' || detail.receiptStatus === 'Siap Cetak' ? 'is-paid' : 'is-pending'}`;
                detailFields.time.textContent = detail.time || '-';
                detailFields.quantity.textContent = detail.quantity || '-';
                detailFields.notes.textContent = detail.notes || '-';

                detailPrint?.classList.toggle('d-none', !detail.canPrint || !detail.printUrl);
                if (detailPrint && detail.printUrl) {
                    detailPrint.href = detail.printUrl;
                }

                detailFinishForm?.classList.toggle('d-none', !detail.canFinish || !detail.verifyUrl);
                if (detailFinishForm && detail.verifyUrl) {
                    detailFinishForm.action = detail.verifyUrl;
                }

                modal.classList.add('show');
                modal.setAttribute('aria-hidden', 'false');
                closeModalButton?.focus();
            };

            rows.forEach((row) => {
                const showDetail = () => {
                    try {
                        openModal(JSON.parse(row.dataset.detail || '{}'));
                    } catch (error) {
                        showToast('Detail transaksi belum bisa dibuka.');
                    }
                };

                row.addEventListener('click', showDetail);
                row.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        showDetail();
                    }
                });
            });

            document.querySelectorAll('[data-receipt-detail]').forEach((button) => {
                button.addEventListener('click', () => {
                    try {
                        openModal(JSON.parse(button.dataset.receiptDetail || '{}'));
                    } catch (error) {
                        showToast('Detail bukti pembayaran belum bisa dibuka.');
                    }
                });
            });

            closeModalButton?.addEventListener('click', closeModal);
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });

            const historySidebar = document.querySelector('[data-history-sidebar]');
            const historyOverlay = document.querySelector('[data-history-overlay]');
            const historyOpenButton = document.querySelector('[data-history-open]');
            const historyCloseButton = document.querySelector('[data-history-close]');
            const historyTabs = [...document.querySelectorAll('[data-history-tab]')];
            const historySections = [...document.querySelectorAll('[data-history-section]')];

            const closeHistory = () => {
                historySidebar?.classList.remove('show');
                historyOverlay?.classList.remove('show');
                historySidebar?.setAttribute('aria-hidden', 'true');
            };

            const openHistory = () => {
                historySidebar?.classList.add('show');
                historyOverlay?.classList.add('show');
                historySidebar?.setAttribute('aria-hidden', 'false');
                historyCloseButton?.focus();
            };

            historyOpenButton?.addEventListener('click', openHistory);
            historyCloseButton?.addEventListener('click', closeHistory);
            historyOverlay?.addEventListener('click', closeHistory);

            historyTabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const target = tab.dataset.historyTab;
                    historyTabs.forEach((item) => item.classList.toggle('active', item === tab));
                    historySections.forEach((section) => {
                        section.classList.toggle('active', section.dataset.historySection === target);
                    });
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeHistory();
                }
            });
        })();
    </script>
@endsection
