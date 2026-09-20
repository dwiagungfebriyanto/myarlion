# Return Stock & Sample Feature

## Overview
This feature allows users to return "Inventory Stock" and "Inventory Sample" items back to the system. This is typically used when stock or samples need to be removed from the active inventory list and tracked as returned items, often generating "Other Income" or simply adjusting the stock levels.

## Technical Architecture

### 1. Database Schema
The main table involved is `return_stocks`.

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | BigInt | Primary Key |
| `warehouse_id` | Foreign Key | References `warehouses.id`. The warehouse where the stock was held. |
| `inventory_stock_id` | Foreign Key | References `inventory_stocks.id`. The specific stock/sample being returned. |
| `other_income_id` | Foreign Key | References `other_incomes.id`. Optional link if the return generates income. |
| `qty` | Double | The quantity being returned. |
| `total` | Double | The total monetary value of the return (`qty` * unit price). |
| `created_at` | Timestamp | |
| `updated_at` | Timestamp | |

### 2. Models
- **`App\Models\ReturnStock`**:
    - Relationships: `belongsTo` Warehouse, `belongsTo` InventoryStock.
    - Attributes: `supplier` (accessor that traverses back to the original PO to find the supplier).
- **`App\Models\InventoryStock`**:
    - The source of the stock. It distinguishes between standard stock and samples via the `inventory_type` column (e.g., `'in'`, `'out'`, `'sample'`, `'mutation'`).

### 3. Controllers
- **`App\Http\Controllers\InventoryStockController`**:
    - **`index(Request $request)`**: Displays the list of stocks. Filters by `type=sample` to show samples or defaults to showing standard stocks.
    - **View**: `pages.inventory_stock.index`.
    - **Key Logic**: Includes the `create-return-modal` component and adds a "Return" action button to the datatable if the stock quantity > 0.

- **`App\Http\Controllers\ReturnStockController`**:
    - **`store(StoreReturnStockRequest $request)`**:
        - Validates the request.
        - Checks if ample stock exists (`$inventoryStock->getStock()`).
        - **Session Handling**: Instead of saving immediately, it stores the return data in the session (`pending_return_stock`) and redirects to `/accounting/other-income` (presumably to allow the user to link this return to an income transaction).
    - **`saveFromSession(Request $request)`**:
        - Retrieves data from `pending_return_stock` session.
        - Re-validates stock availability.
        - Creates the `ReturnStock` record.
        - **Stock Adjustment**: Calls `updateInventoryStock` to decrement the `amount` or `weight` in `inventory_stocks` table.
    - **`destroy(ReturnStock $returnStock)`**:
        - Deletes the return record.
        - **Restores Stock**: Adds the returned quantity back to the `InventoryStock` (`amount` or `weight`).
        - Optionally deletes the associated `OtherIncome` record if requested.

### 4. Views
- **`resources/views/pages/inventory_stock/index.blade.php`**:
    - Displays the datatable.
    - Conditionally renders the "Return" button: `@if($inventoryStock->unitValue() > 0)`.
- **`resources/views/pages/inventory_stock/components/create-return-modal.blade.php`**:
    - A Modal form to input return details.
    - Uses **AutoNumeric** for quantity inputs.
    - Uses **Event Delegation** (`$('body').on('click', '.btn-return', ...)`) to attach click handlers to the Return buttons, ensuring compatibility with Datatable pagination.
    - Submits data via AJAX to `ReturnStockController@store`.

## User Flow

1.  **Select Stock**: User navigates to **Inventory Stock** (or Inventory Sample).
2.  **Initiate Return**: User clicks the "Return" button on a specific row.
3.  **Input Details**:
    - Modal opens with pre-filled Warehouse, Product, and Price information.
    - User inputs `Quantity`.
    - System calculates `Total`.
4.  **Confirm**: User clicks "Add".
5.  **Processing**:
    - System validates stock availability.
    - Data is saved to Session.
    - User is redirected to the **Other Income** page to finalize the financial transaction (if applicable).

## Development Notes
- **Stock Validation**: The system strictly prevents returning more stock than is currently available.
- **Unit Handling**: The system distinguishes between Unit IDs (e.g., ID 1 for Kg/Weight, ID 2 for Pcs/Amount) when updating stock levels.
- **Data Integrity**: When a return is deleted, the stock is automatically restored to the inventory to maintain accurate counts.

