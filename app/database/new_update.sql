-- Queries from add_pandit_location_fields.php
ALTER TABLE SAI_pandit_profiles ADD COLUMN latitude DECIMAL(10, 8) NULL AFTER average_rating;
ALTER TABLE SAI_pandit_profiles ADD COLUMN longitude DECIMAL(11, 8) NULL AFTER latitude;
ALTER TABLE SAI_pandit_profiles ADD COLUMN city VARCHAR(100) NULL AFTER longitude;
ALTER TABLE SAI_pandit_profiles ADD COLUMN pincode VARCHAR(20) NULL AFTER city;
ALTER TABLE SAI_pandit_profiles ADD COLUMN service_area_km INT DEFAULT 50 AFTER pincode;

-- Query from add_pandit_google_map_link
ALTER TABLE SAI_pandit_profiles ADD COLUMN map_url VARCHAR(500) NULL AFTER longitude;
