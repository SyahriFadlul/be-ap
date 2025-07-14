<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Goods;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GoodsFieldsFillerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   // run obatImport api first
        // filter: stock > 4 , unit [strip], data max 30
        // default category = obat bebas, obat resep, obat herbal, suplemen 
        $goods = Goods::all();
        $sortedMatchGoodsCategory = [
            2, // ACETYLSISTEIN – resep - 10 strip - 10 tablet
            2, // ACYCLOVIR 400 MG – resep - 5 - 10
            3, // ALADINA CAPSUL – herbal - 12 - 2
            2, // ALLOPURINOL 100MG – resep - 10 - 10 
            5, // ALPARA TABLET – bebas - 15 - 10 
            1, // ALPHAMOL – bebas - 15 - 10
            2, // AMBROXOL 30 MG – resep - 10 - 10
            2, // AMLODIPIN 10 GR – resep - 10 - 10
            2, // AMOXICILLIN 500 mg – resep - 10 - 10
            2, // ANASTAN FORTE – resep - 5 - 10
            2, // ANDALAN PIL KB – resep - 2 - 28
            3, // ANTANGIN TABLET – herbal - 20 - 4
            1, // ANTASIDA DOEN TABLET – bebas - 10 - 10
            1, // ANTIMO TABLET – bebas - 72 - 10
            4, // ARKAVIT – suplemen - 10 - 10
            2, // ASAM MEFENAMAT 500 MG – resep - 10 - 10
            2, // ATORVASTATIN 20MG – resep - 3 - 10
            4, // BECOM C – suplemen - 10 - 10
            1, // BIOGESIC – bebas - 25 - 4
            2, // BISOPROLOL 5MG – resep - 10 - 10
            1, // BODREX – bebas - 2 - 10
            2, // CARGESIK 500MG – resep - 10 - 10
            4, // CALCIUM LACTAT – suplemen - 10 - 10
            1, // CALORTUSIN – bebas - 10 - 10
            2, // CAPTOPRIL 25MG – resep - 10 - 10
            2, // CARBIDU 0,5 MG – resep - 20 - 10
            2, // CARDIO ASPIRIN - resep - 3 - 10
            4, // CAVIPLEX – suplemen - 10 - 10
            2, // CEFIXIME 100MG – resep - 10 - 10
            1, // CETEME - bebas - 10 - 10
        ];

       $conversion_large_to_medium = [
            10, 5, 12, 10, 15, 15, 10, 10, 10, 5,  
            2, 20, 10, 72, 10, 10, 3, 10, 25, 10, 
            2, 10, 10, 10, 10, 20, 3, 10, 10, 10, 
        ];

        $conversion_medium_to_base = [
            10, 10, 2, 10, 10, 10, 10, 10, 10, 10,
            28, 4, 10, 10, 10, 10, 10, 10, 4, 10,
            10, 10, 10, 10, 10, 10, 10, 10, 10, 10,
        ];

        foreach ($goods as $i => $good) {
            $categoryId = $sortedMatchGoodsCategory[$i] ?? null;
            $largeToMed = $conversion_large_to_medium[$i] ?? null;
            $medToBase = $conversion_medium_to_base[$i] ?? null;

            if ($categoryId !== null && $largeToMed !== null && $medToBase !== null) {
                $good->update([
                    'category_id' => $categoryId,
                    'conversion_large_to_medium' => $largeToMed,
                    'conversion_medium_to_base' => $medToBase,
                ]);
            }
        }
    }

}
