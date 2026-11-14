<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Membership Packages (PRD 5.2)
        if (! Schema::hasTable('membership_packages')) {
            Schema::create('membership_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->unsignedInteger('duration_days')->default(30);
                $table->unsignedInteger('price');
                $table->text('facilities')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Memberships history (PRD 5.2)
        if (! Schema::hasTable('memberships')) {
            Schema::create('memberships', function (Blueprint $table) {
                $table->id();
                $table->foreignId('member_id')->constrained('gym_members')->cascadeOnDelete();
                $table->foreignId('package_id')->nullable()->constrained('membership_packages')->nullOnDelete();
                $table->foreignId('transaction_id')->nullable()->constrained('cashier_transactions')->nullOnDelete();
                $table->date('start_date');
                $table->date('end_date');
                $table->unsignedInteger('price');
                $table->string('status', 20)->default('active'); // pending, active, expired, suspended, cancelled
                $table->text('status_reason')->nullable();
                $table->timestamps();

                $table->index(['member_id', 'status']);
                $table->index(['start_date', 'end_date']);
            });
        }

        // 3. Expense Categories (PRD 5.2)
        if (! Schema::hasTable('expense_categories')) {
            Schema::create('expense_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 4. Gym Settings (PRD 5.2)
        if (! Schema::hasTable('gym_settings')) {
            Schema::create('gym_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // 5. Activity Logs (PRD 5.2)
        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 100);
                $table->string('entity_type', 100)->nullable();
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->text('description')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index(['user_id', 'action']);
                $table->index(['entity_type', 'entity_id']);
            });
        }

        // 6. Add extra fields to gym_members (gender, birth_date, address)
        Schema::table('gym_members', function (Blueprint $table) {
            if (! Schema::hasColumn('gym_members', 'gender')) {
                $table->string('gender', 20)->nullable()->after('full_name');
            }
            if (! Schema::hasColumn('gym_members', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('gender');
            }
            if (! Schema::hasColumn('gym_members', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
        });

        // 7. Add cashier_id & discount to cashier_transactions
        Schema::table('cashier_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('cashier_transactions', 'cashier_id')) {
                $table->foreignId('cashier_id')->nullable()->after('gym_member_id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('cashier_transactions', 'discount')) {
                $table->unsignedInteger('discount')->default(0)->after('amount');
            }
            if (! Schema::hasColumn('cashier_transactions', 'package_id')) {
                $table->foreignId('package_id')->nullable()->after('product_id')->constrained('membership_packages')->nullOnDelete();
            }
        });

        // 8. Add is_active to users
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });

        // 9. Add category_id to expense_records
        Schema::table('expense_records', function (Blueprint $table) {
            if (! Schema::hasColumn('expense_records', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('id')->constrained('expense_categories')->nullOnDelete();
            }
            if (! Schema::hasColumn('expense_records', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('category_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('gym_settings');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('membership_packages');
    }
};
