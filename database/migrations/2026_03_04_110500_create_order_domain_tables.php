<?php

use App\Enums\OrderStatus;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Наименование товара');
            $table->string('sku')->unique()->comment('Артикул товара');
            $table->decimal('price', 12, 2)->comment('Цена товара');
            $table->unsignedInteger('stock_quantity')->comment('Остаток на складе');
            $table->string('category')->comment('Категория товара');
            $table->timestamps();
            $table->comment('Каталог товаров');

            $table->index('category');
            $table->index('name');
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ФИО клиента');
            $table->string('email')->unique()->comment('Электронная почта клиента');
            $table->string('phone', 32)->comment('Телефон клиента');
            $table->timestamps();
            $table->comment('Клиенты');

            $table->index('phone');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('status', 24)->default(OrderStatus::NEW->value)->comment('Статус заказа');
            $table->decimal('total_amount', 14, 2)->default(0)->comment('Итоговая сумма заказа');
            $table->timestamp('confirmed_at')->nullable()->comment('Время подтверждения заказа');
            $table->timestamp('shipped_at')->nullable()->comment('Время отгрузки заказа');
            $table->timestamps();
            $table->comment('Заказы');

            $table->index(['status', 'created_at']);
            $table->index(['customer_id', 'created_at']);
            $table->index('created_at');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity')->comment('Количество товара');
            $table->decimal('unit_price', 12, 2)->comment('Цена за единицу');
            $table->decimal('total_price', 14, 2)->comment('Сумма по позиции');
            $table->timestamps();
            $table->comment('Позиции заказа');

            $table->unique(['order_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('products');
    }
};
