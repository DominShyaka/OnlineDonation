USE online_donation_platform;

-- Add account suspension support
ALTER TABLE users
ADD COLUMN account_status ENUM('active', 'suspended')
NOT NULL DEFAULT 'active' AFTER role;

-- Add NGO approval workflow
ALTER TABLE ngos
ADD COLUMN approval_status ENUM('pending', 'approved', 'rejected')
NOT NULL DEFAULT 'pending',
ADD COLUMN reviewed_at DATETIME NULL,
ADD COLUMN rejection_reason VARCHAR(255) NULL;

