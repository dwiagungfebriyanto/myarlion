<?php

use App\Models\InventoryStock;
use App\Models\Job;
use App\Models\OutcomeGroup;
use App\Models\PoAsset;
use App\Models\PoStock;
use App\Models\Product;
use App\Models\SalesTarget;
use App\Models\SalesTargetMonthly;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

function APIresponse(bool $success, string $message, $data = null)
{
    return [
        'success' => $success,
        'message' => $message,
        'data'    => $data
    ];
}

function cleanCurrencyFormat($param)
{
    if (!is_numeric($param)) {
        $param = substr($param, 0, -2);
    }

    return preg_replace("/\D/", "", $param);
}

if (!function_exists('camelCaseToSpaces')) {
    function camelCaseToSpaces($string)
    {
        return preg_replace('/(?<!^)([A-Z])/', ' $1', $string);
    }
}

function costCodeOptions($selectedOption = null, $codeType = null)
{
    # base options
    $options     = '<option disabled selected>-- Select Code --</option>';
    $baseOptions = ['all', 'CF', 'KB', 'DEV'];


    foreach ($baseOptions as $baseOption) {
        $selected = ($selectedOption === $baseOption) ? 'selected' : '';

        $options .= "<option value='$baseOption' data-code-type='$baseOption' $selected>$baseOption</option>";
    }

    # opened job options
    $openedJobs = Job::where('status', 'open')->get();

    foreach ($openedJobs as $openedJob) {
        $selected = (($selectedOption == $openedJob->id) && ($codeType === 'job')) ? 'selected' : '';

        $customerName = $openedJob->customer->name ?? '';
        $customerCode = $openedJob->customer->code ?? '';

        $options .= "<option value='$openedJob->id' data-code-type='job' $selected>"
            . "Job #$openedJob->code : $customerName - $customerCode"
            . "</option>";
    }

    # PO stock options
    foreach (PoStock::all() as $poStock) {
        $selected = (($selectedOption == $poStock->id) && ($codeType === 'po_stock')) ? 'selected' : '';

        $options .= "<option value='$poStock->id' data-code-type='po_stock' $selected>
                        PO Stock: $poStock->unique_id"
            . "</option>";
    }

    # PO stock asset
    foreach (PoAsset::all() as $poAsset) {
        $selected = (($selectedOption == $poAsset->id) && ($codeType === 'po_asset')) ? 'selected' : '';

        $options .= "<option value='$poAsset->id' data-code-type='po_asset' $selected>
                            PO Asset: $poAsset->unique_id - " . $poAsset->supplier->supplier_name
            . "</option>";
    }

    return $options;
}

function currencyFormat($nominal, $prefix = 'Rp ')
{
    return "$prefix " . number_format($nominal, 2, ',', '.');
}

function humanizeDate(string $date) : string
{
    return Carbon::parse($date)->diffForHumans();
    
}

function isActiveRoute($route)
{
    return (Route::currentRouteName() === $route) ? 'active' : '';
}

function outcomeGroup($id)
{
    return OutcomeGroup::find($id);
}

if (!function_exists('outputCodeByType')) {
    function outputCodeByType($code, $codeType)
    {
        switch ($codeType) {
            case 'job':
                $job      = Job::find($code);
                $customer = $job->customer;
                $url      = "<a href='" . route('job_statement.index', $job->id) ."' target='_blank'>$job->code</a>";

                return "Job: #{$url} - $customer->name";
                break;

            case 'po_stock':
                $poStock = PoStock::find($code);
                $url     = "<a href='" . route('po_stock.detail', $poStock->id) ."' target='_blank'>$poStock->unique_id</a>";

                return "PO Stock: $url";
                break;

            case 'po_asset':
                $poAsset = PoAsset::find($code);

                return "PO Asset: $poAsset->unique_id";
                break;

            default:
                return $code;
                break;
        }
    }
}

function selectGenerate($title, $list, $valueName, $textNames, $selected = null, $separator = '-')
{
    if ($title !== null) {
        // jika misal $title = "All Marketing"
        $allWords = ['All', 'All ', 'all', 'all '];
        $allText = substr($title, 0, 4); // dicari "All " atau "all "
        $firstCondition = in_array($allText, $allWords)
            ? 'value="all"'
            : 'disabled';

        $options = "<option selected $firstCondition>-- Select $title --</option>";
    } else {
        $options = "";
    }


    foreach ($list as $data) {
        $isSelected = ($data[$valueName] == $selected)
            ? 'selected'
            : '';

        $texts = '';

        if (is_array($textNames)) {
            foreach ($textNames as $indexKey => $key) {
                $prefix = ($indexKey !== 0) ? ' ' . $separator . ' ' : '';

                $texts .= $prefix . $data[$key];
            }
        } else {
            $texts .= $data[$textNames];
        }

        $options .= '<option value="' . $data[$valueName] . '"' . $isSelected . '>'
            . $texts . '</option>';
    }

    return $options;
}

function updateAvgOnAddInventory(Product $product, $costPerPcs = null, $orderQty = null)
{
    // jika first record
    if (count($product->inventoryStocks) === 0) {
        $product->harga_rata_rata = $costPerPcs;
        $product->save();
    } else {
        if ($orderQty != 0) {
            countProductAvgprice($product, $orderQty, $costPerPcs);
        }
    }

    return 0;
}

function updateAvgOnDeleteInventory(Product $product)
{
    // jika $LatestInventoryStock ada berarti
    // yang dihapus bukan record pertama
    if ($latestInventoryStock2nd = $product->inventoryStocks()->latest()->skip(1)->first()) {

        $latestInventoryStock = $product->inventoryStocks()->latest()->first();

        $unit = ($product->unit->unit_name === 'Kg') ? 'weight' : 'amount';

        $orderQty   = $latestInventoryStock->$unit;
        $costPerPcs = $latestInventoryStock->purchase_cost_per_unit;
        $latestAvg  = $latestInventoryStock2nd->history_overall_avg;

        countProductAvgprice($product, $orderQty, $costPerPcs, $latestAvg);
    } else {
        $product->harga_rata_rata = 0;
        $product->save();
    }

    return 0;
}

function updateTargetAchievements(User $marketing, string $period)
{
    $yearPeriod = Str::limit($period, 4, '');
    $yearMonthPeriod = Str::limit($period, 7, '');

    SalesTarget::updateMarketingSalestarget($marketing, $yearPeriod);
    SalesTargetMonthly::updateMarketingSalestarget($marketing, $yearMonthPeriod);
}

/*
    saat pemanggilan countProductAvgprice()
    letakkan setelah updateProductStock()
*/
function countProductAvgprice(Product $product, $orderQty, $costPerPcs, $latestAvg = null)
{
    $latestStock = $product->qty;

    if (is_null($latestAvg)) {
        $latestAvg   = $product->harga_rata_rata;
    }

    $avg = (($latestStock * $latestAvg) + ($orderQty * $costPerPcs))
        / ($latestStock + $orderQty);

    $product->harga_rata_rata = $avg;
    $product->save();

    if ($latestInventoryStock = $product->inventoryStocks()->latest()->first()) {
        $latestInventoryStock->history_overall_avg = $avg;
        $latestInventoryStock->save();
    }

    return 0;
}

function updateHighestPriceProduct(Product $product)
{
    $usedValue = ($product->unit_id === 2) ? 'amount' : 'weight';

    $highestPrice = $product->inventoryStocks()
        ->where('unit_id', $product->unit_id)
        ->where($usedValue, '>', 0)
        ->max('purchase_cost_per_unit');

    $product->harga_tertinggi = $highestPrice;
    $product->save();

    return 0;
}

function updateProductStock(Product $product, InventoryStock $inventoryStock = null)
{
    $usedUnit = ($product->unit_id === 2) ? 'amount' : 'weight';

    $productStock = $product->inventoryStocks()->sum($usedUnit);

    $product->qty = $productStock;
    $product->save();

    if ($inventoryStock) {
        $inventoryStock->history_overall_qty = $productStock;
        $inventoryStock->save();
    }

    return 0;
}

function syncInventoryStockHistory(InventoryStock $inventoryStock)
{
    $product = $inventoryStock->product;

    if (!$product) {
        return 0;
    }

    $inventoryStock->history_overall_qty = $product->qty;
    $inventoryStock->history_overall_avg = $product->harga_rata_rata ?? 0;
    $inventoryStock->save();

    return 0;
}

// Job helper
function percentOutstanding($amount, $payment)
{
    return number_format(($payment / $amount) * 100, 2);
}

// For make flexible code, we can make code without max code, but we need to make sure that the code is unique and fill the gap of queue code
function generateCode($array_model, int $initial_code)
{
    $array_code = array_column($array_model, 'code');
    // how to change the code to number
    $array_numb = array_map('intval', $array_code);
    sort($array_numb);

    $array_number = [];
    foreach ($array_numb as $number) {
        if ($number >= $initial_code) {
            $array_number[] = $number;
        }
    }

    if (count($array_number) == 0) {
        $next_code = $initial_code;
    } else {
        if ($array_number[0] != $initial_code) {
            $next_code = $initial_code;
        } else {
            if (count($array_number) > 1) {
                for ($i = 0; $i < count($array_number) - 1; $i++) {
                    if ($array_number[$i + 1] - $array_number[$i] > 1) {
                        $next_code = $array_number[$i] + 1;
                        return $next_code;
                        break;
                    }
                    $next_code = $array_number[$i + 1] + 1;
                }
            } elseif (count($array_number) == 1) {
                if ($array_number[0] > $initial_code) {
                    $next_code = $initial_code;
                } else {
                    $next_code = $array_number[0] + 1;
                }
            }
        }
    }

    // dd($next_code);

    return $next_code;
}
