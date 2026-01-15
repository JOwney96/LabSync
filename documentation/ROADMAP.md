# Project Roadmap: LabSync

This document outlines the development stages for the LabSync University Checkout System. Completion of each phase is a milestone.

## Phase 1: Foundation & Infrastructure
**Goal:** Get the environment running and the "skeleton" of the app in place.
- [X] Initialize Laravel Project (Git repo setup).
- [ ] Configure AWS RDS (Database) and connect `.env`.
- [ ] Configure AWS S3 (File Storage) for image uploads.
- [ ] Implement Authentication (Laravel Breeze or Jetstream).
- [ ] Define User Roles (Add `role` column: 'student' vs 'admin').

## Phase 2: Inventory Management (The "Noun" Layer)
**Goal:** Allow Admins to manage the physical items.
- [ ] **Database Migration:** Create `categories` and `items` tables.
- [ ] **Admin UI:** Create forms to Add/Edit/Delete equipment.
- [ ] **Image Logic:** Implement file upload to S3 when creating an item.
- [ ] **Catalog View:** Create a public page where Students can view (but not reserve) items.

## Phase 3: The Reservation Engine (The "Verb" Layer)
**Goal:** The core business logic. This is the most complex phase.
- [ ] **Database Migration:** Create `reservations` table.
- [ ] **Backend Service:** Create `ReservationService` class.
    - Logic: `checkAvailability(itemId, startDate, endDate)`
    - Logic: Prevent overlapping dates.
- [ ] **Frontend (Alpine.js):** Create the Date Picker component that calls the backend check.
- [ ] **Checkout Logic:** Connect the "Reserve" button to the database.

## Phase 4: Workflow & Dashboards
**Goal:** Managing the lifecycle of a reservation.
- [ ] **Student Dashboard:** View "My Reservations" and status.
- [ ] **Admin Dashboard:** View "Pending Approvals" list.
- [ ] **Action Buttons:** Implement Approve/Deny (Admin) and Cancel (Student) logic.
- [ ] **Check-in/Check-out:** Admin interface to mark items as "Picked Up" or "Returned."

## Phase 5: Polish & Automation (Reach Goals)
**Goal:** Improving UX and adding system automation.
- [ ] **Email Notifications:** Trigger emails on status changes (Reserved, Approved).
- [ ] **Overdue Scripts:** Create a scheduled task (Cron) to flag late returns.
- [ ] **Search/Filter:** Improve the Catalog search (e.g., "Show only available Cameras").
