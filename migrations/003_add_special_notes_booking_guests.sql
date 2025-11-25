-- Migration: add special_notes column to booking_guests
-- Thêm cột `special_notes` (ghi chú đặc biệt) cho từng khách
ALTER TABLE booking_guests
ADD COLUMN special_notes TEXT NULL DEFAULT '';
