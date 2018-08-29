<?php
use App\PaymentMethod;
use App\Brand;
use App\ProductCategory;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pamentMethod = new PaymentMethod();
        $pamentMethod->Type ="Cash";
        $pamentMethod->save();

        $pamentMethod = new PaymentMethod();
        $pamentMethod->Type ="Visa";
        $pamentMethod->save();

        $brand = new Brand();
        $brand->name ="Test brand";
        $brand->save();

        $style = new ProductCategory();
        $style->name ="Test style";
        $style->save();
    }
}
