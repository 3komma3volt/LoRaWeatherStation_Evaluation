-- Weather Station Database Schema
-- Import this SQL file in phpMyAdmin to create the required tables

-- Create weather_data table
CREATE TABLE weather_data (
    id INT AUTO_INCREMENT NOT NULL, 
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, 
    app_id VARCHAR(50) DEFAULT NULL, 
    dev_id VARCHAR(22) NOT NULL, 
    ttn_time VARCHAR(50) DEFAULT NULL, 
    gtw_id VARCHAR(255) DEFAULT NULL, 
    gtw_rssi INT DEFAULT NULL, 
    gtw_snr INT DEFAULT NULL, 
    data_temperature DOUBLE PRECISION DEFAULT NULL, 
    data_humidity DOUBLE PRECISION DEFAULT NULL, 
    data_pressure DOUBLE PRECISION DEFAULT NULL, 
    data_battery DOUBLE PRECISION DEFAULT NULL, 
    data_wind DOUBLE PRECISION DEFAULT NULL, 
    data_winddir DOUBLE PRECISION DEFAULT NULL, 
    data_brightness DOUBLE PRECISION DEFAULT NULL, 
    data_radiation DOUBLE PRECISION DEFAULT NULL, 
    data_rain DOUBLE PRECISION DEFAULT NULL, 
    data_uv DOUBLE PRECISION DEFAULT NULL, 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- Create weather_stations table
CREATE TABLE weather_stations (
    id INT AUTO_INCREMENT NOT NULL, 
    dev_id VARCHAR(22) NOT NULL, 
    alias VARCHAR(255) DEFAULT NULL, 
    description VARCHAR(255) DEFAULT NULL, 
    latitude DOUBLE PRECISION DEFAULT NULL, 
    longitude DOUBLE PRECISION DEFAULT NULL, 
    altitude DOUBLE PRECISION DEFAULT NULL, 
    status JSON DEFAULT NULL, 
    last_update DATETIME DEFAULT CURRENT_TIMESTAMP, 
    UNIQUE INDEX UNIQ_465DD212A421F7B0 (dev_id), 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- Create doctrine_migration_versions table (required by Symfony)
CREATE TABLE doctrine_migration_versions (
    version VARCHAR(191) NOT NULL,
    executed_at DATETIME DEFAULT NULL,
    execution_time INT DEFAULT NULL,
    PRIMARY KEY(version)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- Insert the migration record
INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) 
VALUES ('DoctrineMigrations\\Version20250423195108', NOW(), 1);
