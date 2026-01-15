# LabSync: University Equipment Checkout System
LabSync is a full-stack web application designed to streamline the process of borrowing and managing university lab equipment. It handles the lifecycle of inventory—from student reservation requests to administrative approval and physical check-in/check-out.

# Key Features:

Real-time Availability: Prevents double-booking with date-range conflict detection.

Role-Based Access: Distinct workflows for Students (Request) and Admins (Approve/Manage).

Inventory Tracking: Monitors item status (Available, Reserved, Checked Out, Maintenance).

Automated Alerts: Overdue detection and email notifications using AWS services.

Infrastructure: Built to run on the AWS Free Tier (EC2, RDS, S3).
