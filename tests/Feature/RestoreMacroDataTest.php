<?php

namespace Tests\Feature;

use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use Database\Seeders\IndikatorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestoreMacroDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_macro_dataset_includes_all_18_indicators_and_full_2005_to_2023_history(): void
    {
        $this->seed(IndikatorSeeder::class);

        $this->assertEquals(18, Indikator::count());

        $capaiansYears = Capaian::query()->pluck('tahun')->unique()->sort()->values();
        $targetsYears = Target::query()->pluck('tahun')->unique()->sort()->values();

        $this->assertSame([2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023], $capaiansYears->all());
        $this->assertSame($capaiansYears->all(), $targetsYears->all());
        $this->assertEquals(18 * 19, Capaian::count());
        $this->assertEquals(18 * 19, Target::count());
    }
}
