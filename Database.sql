//Product_tab)


USE product_db;
CREATE TABLE `product_test` (
                                `id` int(11) NOT NULL,
                                `name` varchar(128) NOT NULL,
                                `description` text DEFAULT NULL,
                                `size` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `product_test`
    ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

ALTER TABLE `product_test`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

INSERT INTO `product_test` (`name`, `description`, `size`) VALUES
                                                               ('Product One', NULL, 10),
                                                               ('Product Two', 'example', 20);




//user_tab)


USE product_db;
CREATE TABLE `user` (
                        id int(11) NOT NULL,
                        name varchar(128) NOT NULL,
                        email varchar(255) NOT NULL,
                        password_hash varchar(255) NOT NULL,
                        api_key varchar(255) NOT NULL,
                        api_key_hash varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE user
    ADD PRIMARY KEY (id),
  ADD UNIQUE KEY email (email),
  ADD UNIQUE KEY api_key_hash (api_key_hash);


ALTER TABLE user
    MODIFY id int(11) NOT NULL AUTO_INCREMENT;




//add_google_auth_to_user_table)

ALTER TABLE user
    ADD COLUMN google_id VARCHAR(255) NULL AFTER api_key_hash,
ADD COLUMN avatar VARCHAR(255) NULL AFTER google_id,
ADD UNIQUE KEY google_id (google_id);


//add_file_handling_to_product)


USE product_db;
ALTER TABLE `product_test`
    ADD COLUMN `file_name` VARCHAR(255) NULL AFTER `size`,
ADD COLUMN `file_path` VARCHAR(255) NULL AFTER `file_name`,
ADD COLUMN `file_type` VARCHAR(100) NULL AFTER `file_path`,
ADD COLUMN `file_size` INT NULL AFTER `file_type`;