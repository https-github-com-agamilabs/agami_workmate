CREATE TABLE `asp_task_reminder_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cblscheduleno` INT NULL,
    `userno` INT NOT NULL,
    `reminder_type` VARCHAR(50) NOT NULL, -- 'morning', 'lagging', 'stalled'
    `sent_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
