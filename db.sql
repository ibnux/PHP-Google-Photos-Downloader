
CREATE TABLE `t_photos` (
    `id` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
    `root_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
    `description` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
    `album_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
    `baseUrl` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `productUrl` varchar(512) COLLATE utf8mb4_general_ci NOT NULL,
    `mimeType` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
    `filename` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
    `mediaMetadata` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    `hash` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
    `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `t_photos`
    ADD PRIMARY KEY (`id`),
    ADD KEY `hash` (`hash`);
COMMIT;
