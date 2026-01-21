# System Architecture

## Technology Stack

- **Frontend:** Laravel Livewire
    - **Interactivity:** Alpine.js (Lightweight JS for modals, date pickers)
    - **Styling:** Tailwind CSS
- **Backend:** Laravel 12 (PHP)
- **Database:** MySQL 8.0 (AWS RDS) or SQLite managed by Laravel
- **File Storage:** AWS S3
- **Server:** AWS EC2 (Ubuntu/Amazon Linux)

## Data Flow: Checking Availability

Since we are using Alpine.js + Laravel, the flow for checking item availability is:

1. **User** selects a Date Range on the Frontend (Blade View).
2. **Alpine.js** detects the `change` event on the input.
3. **Alpine.js** sends an asynchronous `fetch()` request to `/api/availability`.
4. **Laravel Controller** calls the `ReservationService`.
5. **Service** queries the SQL Database:
   ```sql
   SELECT count(*) FROM reservations 
   WHERE item_id = ? 
   AND (
       (start_date <= requested_end) AND (end_date >= requested_start)
   )
   AND status NOT IN ('rejected', 'returned')
   ```
6. **Laravel** returns JSON `{ "available": true/false }`.
7. **Alpine.js** updates the "Reserve" button state (Enable/Disable).

## Directory Structure Highlights

- `app/Services/`: Contains business logic (e.g., `ReservationService.php`).
- `app/Http/Controllers/`: Handles incoming requests and returns Views.
- `resources/views/`: Blade templates.
- `routes/web.php`: Standard page routes.
- `routes/api.php`: Endpoints for Alpine.js AJAX calls.
