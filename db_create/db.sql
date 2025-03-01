SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `chat_has_message` (
  `chat_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `chat` (
  `chat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `chat_has_chat` (
  `parent_chat_id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `contact` (
  `contact_id` int(11) NOT NULL,
  `contact_name` varchar(45) DEFAULT NULL,
  `contact_parent_id` int(11) DEFAULT NULL,
  `contact_deleted` int(10) UNSIGNED DEFAULT NULL,
  `contact_created` int(10) UNSIGNED NOT NULL DEFAULT unix_timestamp(),
  `contact_edited` int(10) UNSIGNED DEFAULT NULL,
    `chat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `contact_has_field_value` (
  `contact_id` int(11) NOT NULL,
  `field_value_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `contact_has_job` (
  `contact_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `contact_has_tag` (
  `contact_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `field` (
  `field_id` int(11) NOT NULL,
  `field_name` varchar(45) NOT NULL,
  `field_view_name` varchar(45) DEFAULT NULL,
  `field_scope_id` int(11) NOT NULL,
  `field_type_id` int(11) NOT NULL,
  `field_sub_type_id` int(11) DEFAULT NULL,
  `field_min_count` tinyint(4) NOT NULL DEFAULT 0,
  `field_max_count` tinyint(4) NOT NULL DEFAULT 0,
  `field_icon_name` varchar(45) DEFAULT NULL,
  `field_priority` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `field_array` (
  `field_value_parent_id` int(11) NOT NULL,
  `field_value_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `field_char_value` (
  `field_char_value` varchar(15000) DEFAULT NULL,
  `field_value_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `field_double_value` (
  `field_double_value` double DEFAULT NULL,
  `field_value_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `field_integer_value` (
  `field_integer_value` int(11) DEFAULT NULL,
  `field_value_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `field_scope` (
  `field_scope_id` int(11) NOT NULL,
  `field_scope_name` varchar(45) DEFAULT NULL,
  `module_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL COMMENT 'Null if not sensetive of user group',
  `group_read` bit(1) NOT NULL DEFAULT b'1',
  `group_write` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `field_set_values` (
  `field_id` int(11) NOT NULL,
  `field_value_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `field_type` (
  `field_type_id` int(11) NOT NULL,
  `field_type_name` varchar(45) DEFAULT NULL,
  `field_type_base_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `field_value` (
  `field_value_id` int(11) NOT NULL COMMENT 'Maybe char, num, file, but file not db saved',
  `field_id` int(11) DEFAULT NULL COMMENT 'field_id is null then element in array'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `funnel` (
  `funnel_id` int(11) NOT NULL,
  `funnel_name` varchar(45) DEFAULT NULL,
  `field_scope_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(45) DEFAULT NULL,
  `group_parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `group_has_user` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `job` (
  `job_id` int(11) NOT NULL,
  `job_name` varchar(45) NOT NULL,
  `job_description` varchar(1024) DEFAULT NULL,
  `job_date` int(11) DEFAULT NULL,
  `job_create_date` int(11) NOT NULL,
  `job_end_date` int(11) DEFAULT NULL,
  `job_deleted` int(11) DEFAULT NULL,
  `job_period_seconds` int(11) DEFAULT NULL,
  `job_creator` int(11) NOT NULL,
  `job_max_open` tinyint(4) DEFAULT NULL,
  `job_type` tinyint(4) DEFAULT NULL,
  `job_file` int(11) DEFAULT NULL,
  `job_chat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `job_result` (
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_result` varchar(128) DEFAULT NULL,
  `job_result_date` int(10) UNSIGNED DEFAULT NULL,
  `job_cur_date` int(11) NOT NULL,
  `job_result_file` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `message_text` varchar(6200) DEFAULT NULL,
  `message_date` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `message_is_readen` bit(1) NOT NULL DEFAULT b'0',
  `message_file` int(11) DEFAULT NULL,
  `email_send` bit(1) NOT NULL DEFAULT b'0',
  `message_to_id` int(11) DEFAULT NULL,
  `message_date_send` int(11) DEFAULT NULL,
  `email_to_send` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `module` (
  `module_id` int(11) NOT NULL,
  `module_name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `notification_text` varchar(840) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `initiator_id` int(11) DEFAULT NULL,
  `notification_type_send` int(11) DEFAULT NULL,
  `notification_read_date` int(11) DEFAULT NULL,
  `notification_date_send` int(11) DEFAULT NULL,
  `notification_obj_id` int(11) DEFAULT NULL,
  `notification_obj` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL,
  `order_status_id` tinyint(4) NOT NULL,
  `order_user_creator` int(11) NOT NULL,
  `order_user_mentor` int(11) DEFAULT NULL,
  `chat_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `order_sum` decimal(15,0) UNSIGNED DEFAULT NULL,
  `funnel_id` int(11) NOT NULL,
  `order_deleted` int(11) DEFAULT NULL,
  `order_name` varchar(128) DEFAULT NULL,
  `order_create_date` int(11) NOT NULL DEFAULT 0,
  `order_change_date` int(11) DEFAULT 0,
  `order_final_date` int(10) UNSIGNED DEFAULT NULL,
  `order_success` tinyint(4) DEFAULT 0,
  `order_user_worker` int(11) DEFAULT NULL,
  `priority` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_has_field_value` (
  `field_value_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_has_job` (
  `order_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_has_tag` (
  `tag_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_status` (
  `order_status_id` tinyint(4) NOT NULL,
  `order_status_name` varchar(45) DEFAULT NULL,
  `order_status_style` varbinary(1024) DEFAULT NULL,
  `order_status_final` bit(1) DEFAULT NULL COMMENT 'NULL on start, 0 not success, 1 success',
  `field_scope_id` int(11) DEFAULT NULL COMMENT 'May be unnessesary so as in fuels\n',
  `funnel_id` int(11) NOT NULL,
  `order_priority` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_status_history` (
  `order_id` int(11) NOT NULL,
  `order_status_id` tinyint(4) NOT NULL,
  `order_status_history_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `payment_sum` double DEFAULT NULL,
  `payment_currency` char(3) DEFAULT NULL,
  `tarif_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `role` (
  `role_id` tinyint(4) NOT NULL,
  `role_name` varchar(45) DEFAULT NULL COMMENT 'Admin, group manager, regular user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `session` (
  `session_id` binary(32) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tag` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(45) DEFAULT NULL,
  `tag_style` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_login` varchar(45) NOT NULL,
  `user_email` varchar(45) NOT NULL DEFAULT 'RecoveryINfo',
  `user_password` varchar(45) NOT NULL,
  `user_contact_id` int(11) NOT NULL,
  `user_token` varchar(45) DEFAULT NULL,
  `user_token_expire` timestamp NULL DEFAULT NULL,
  `user_active` bit(1) DEFAULT NULL,
  `user_role_id` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_has_device` (
  `token` varchar(255) NOT NULL,
  `session_id` binary(32) DEFAULT NULL,
  `is_active` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_like_contact` (
  `user_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_like_order` (
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `chat_has_message`
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `message_id` (`message_id`);

ALTER TABLE `chat`
  ADD PRIMARY KEY (`chat_id`);

ALTER TABLE `chat_has_chat`
  ADD KEY `chat_has_chat_ibfk_1` (`chat_id`),
  ADD KEY `parent_chat_id` (`parent_chat_id`);

ALTER TABLE `contact`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `fk_Contact_Contact1_idx` (`contact_parent_id`),
  ADD KEY `contact_deleted` (`contact_deleted`),
  ADD KEY `chat_id` (`chat_id`);
;

ALTER TABLE `contact_has_field_value`
  ADD PRIMARY KEY (`contact_id`,`field_value_id`),
  ADD KEY `fk_contact_has_field_value_field_value1_idx` (`field_value_id`),
  ADD KEY `fk_contact_has_field_value_contact1_idx` (`contact_id`);

ALTER TABLE `contact_has_job`
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `job_id` (`job_id`);

ALTER TABLE `contact_has_tag`
  ADD PRIMARY KEY (`contact_id`,`tag_id`),
  ADD KEY `fk_contact_has_tag_tag1_idx` (`tag_id`),
  ADD KEY `fk_contact_has_tag_contact1_idx` (`contact_id`);

ALTER TABLE `field`
  ADD PRIMARY KEY (`field_id`),
  ADD KEY `fk_Fields_FieldsScopes1_idx` (`field_scope_id`),
  ADD KEY `fk_Fields_FieldsTypes1_idx` (`field_type_id`),
  ADD KEY `fk_Fields_FieldsTypes2_idx` (`field_sub_type_id`);

ALTER TABLE `field_array`
  ADD PRIMARY KEY (`field_value_parent_id`,`field_value_id`),
  ADD KEY `fk_FieldArrays_FieldsValuesID2_idx` (`field_value_id`);

ALTER TABLE `field_char_value`
  ADD PRIMARY KEY (`field_value_id`);

ALTER TABLE `field_double_value`
  ADD PRIMARY KEY (`field_value_id`);

ALTER TABLE `field_integer_value`
  ADD PRIMARY KEY (`field_value_id`);

ALTER TABLE `field_scope`
  ADD PRIMARY KEY (`field_scope_id`),
  ADD KEY `fk_FieldsScopes_Modules1_idx` (`module_id`),
  ADD KEY `fk_FieldsScopes_Group1_idx` (`group_id`);

ALTER TABLE `field_set_values`
  ADD PRIMARY KEY (`field_id`,`field_value_id`),
  ADD KEY `fk_table1_field_value1_idx` (`field_value_id`);

ALTER TABLE `field_type`
  ADD PRIMARY KEY (`field_type_id`),
  ADD KEY `fk_FieldsTypes_FieldsTypes1_idx` (`field_type_base_id`);

ALTER TABLE `field_value`
  ADD PRIMARY KEY (`field_value_id`),
  ADD KEY `fk_FieldsValuesID_Fields1_idx` (`field_id`);

ALTER TABLE `funnel`
  ADD PRIMARY KEY (`funnel_id`),
  ADD KEY `fk_Funnel_FieldsScopes1_idx` (`field_scope_id`);

ALTER TABLE `group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `fk_Group_Group1_idx` (`group_parent_id`);

ALTER TABLE `group_has_user`
  ADD PRIMARY KEY (`group_id`,`user_id`),
  ADD KEY `fk_Group_has_User_User1_idx` (`user_id`),
  ADD KEY `fk_Group_has_User_Group1_idx` (`group_id`);

ALTER TABLE `job`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `fk_job_user1_idx` (`job_creator`),
  ADD KEY `job_file` (`job_file`),
  ADD KEY `job_chat_id` (`job_chat_id`);

ALTER TABLE `job_result`
  ADD PRIMARY KEY (`job_id`,`user_id`,`job_cur_date`),
  ADD KEY `fk_job_has_user_user1_idx` (`user_id`),
  ADD KEY `fk_job_has_user_job1_idx` (`job_id`),
  ADD KEY `job_result_file` (`job_result_file`);

ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `fk_Message_Chat1_idx` (`chat_id`),
  ADD KEY `fk_Message_Contact1_idx` (`contact_id`),
  ADD KEY `fk_Message_FieldsValuesID1_idx` (`message_file`);

ALTER TABLE `module`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `ModulesName_UNIQUE` (`module_name`);

ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notification_user1_idx` (`user_id`);
  ADD KEY `fk_notification_initiator11` (`initiator_id`);

ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_Order_OrderStatuses1_idx` (`order_status_id`),
  ADD KEY `fk_Order_User1_idx` (`order_user_creator`),
  ADD KEY `fk_Order_User2_idx` (`order_user_mentor`),
  ADD KEY `fk_Order_Chat1_idx` (`chat_id`),
  ADD KEY `fk_Order_Contact1_idx` (`contact_id`),
  ADD KEY `fk_Order_Funnel1_idx` (`funnel_id`),
  ADD KEY `order_deteted` (`order_deleted`),
  ADD KEY `fk_order_worker` (`order_user_worker`);

ALTER TABLE `order_has_field_value`
  ADD PRIMARY KEY (`field_value_id`,`order_id`),
  ADD KEY `fk_FieldsValuesID_has_Order_Order1_idx` (`order_id`);

ALTER TABLE `order_has_job`
  ADD PRIMARY KEY (`order_id`,`job_id`),
  ADD KEY `fk_Order_has_Job_Job1_idx` (`job_id`),
  ADD KEY `fk_Order_has_Job_Order1_idx` (`order_id`);

ALTER TABLE `order_has_tag`
  ADD PRIMARY KEY (`tag_id`,`order_id`),
  ADD KEY `fk_Tag_has_Order_Order1_idx` (`order_id`),
  ADD KEY `fk_Tag_has_Order_Tag1_idx` (`tag_id`);

ALTER TABLE `order_status`
  ADD PRIMARY KEY (`order_status_id`,`funnel_id`),
  ADD KEY `fk_OrderStatuses_FieldsScopes1_idx` (`field_scope_id`),
  ADD KEY `fk_OrderStatuses_Funnel1_idx` (`funnel_id`);

ALTER TABLE `order_status_history`
  ADD KEY `fk_Order_has_OrderStatuses_OrderStatuses1_idx` (`order_status_id`),
  ADD KEY `fk_Order_has_OrderStatuses_Order1_idx` (`order_id`);

ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_Payments_TarifsHistory1_idx` (`tarif_id`);

ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `fk_session_user1_idx` (`user_id`);

ALTER TABLE `tag`
  ADD PRIMARY KEY (`tag_id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `UserLogin_UNIQUE` (`user_login`),
  ADD KEY `fk_User_Contact1_idx` (`user_contact_id`),
  ADD KEY `fk_user_role1_idx` (`user_role_id`);

ALTER TABLE `user_like_contact`
  ADD PRIMARY KEY (`user_id`,`contact_id`),
  ADD KEY `fk_user_has_contact_contact1_idx` (`contact_id`),
  ADD KEY `fk_user_has_contact_user1_idx` (`user_id`);

ALTER TABLE `user_like_order`
  ADD PRIMARY KEY (`user_id`,`order_id`),
  ADD KEY `fk_user_has_order_order1_idx` (`order_id`),
  ADD KEY `fk_user_has_order_user1_idx` (`user_id`);


ALTER TABLE `chat`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `contact`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `field`
  MODIFY `field_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `field_scope`
  MODIFY `field_scope_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `field_type`
  MODIFY `field_type_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `field_value`
  MODIFY `field_value_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Maybe char, num, file, but file not db saved';

ALTER TABLE `funnel`
  MODIFY `funnel_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `job`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `order_status`
  MODIFY `order_status_id` tinyint(4) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tag`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `contact`
  ADD CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`chat_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Contact_Contact1` FOREIGN KEY (`contact_parent_id`) REFERENCES `contact` (`contact_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `contact_has_field_value`
  ADD CONSTRAINT `fk_contact_has_field_value_contact1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contact_has_field_value_field_value1` FOREIGN KEY (`field_value_id`) REFERENCES `field_value` (`field_value_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `contact_has_job`
  ADD CONSTRAINT `contact_has_job_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `contact_has_job_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `job` (`job_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

ALTER TABLE `contact_has_tag`
  ADD CONSTRAINT `fk_contact_has_tag_contact1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contact_has_tag_tag1` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `field`
  ADD CONSTRAINT `fk_Fields_FieldsScopes1` FOREIGN KEY (`field_scope_id`) REFERENCES `field_scope` (`field_scope_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Fields_FieldsTypes1` FOREIGN KEY (`field_type_id`) REFERENCES `field_type` (`field_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Fields_FieldsTypes2` FOREIGN KEY (`field_sub_type_id`) REFERENCES `field_type` (`field_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `field_array`
  ADD CONSTRAINT `fk_FieldArrays_FieldsValuesID1` FOREIGN KEY (`field_value_parent_id`) REFERENCES `field_value` (`field_value_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_FieldArrays_FieldsValuesID2` FOREIGN KEY (`field_value_id`) REFERENCES `field_value` (`field_value_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `field_char_value`
  ADD CONSTRAINT `fk_FieldCharsValues_FieldsValuesID1` FOREIGN KEY (`field_value_id`) REFERENCES `field_value` (`field_value_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `field_double_value`
  ADD CONSTRAINT `fk_FieldDoubleValues_FieldsValuesID1` FOREIGN KEY (`field_value_id`) REFERENCES `field_value` (`field_value_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `field_integer_value`
  ADD CONSTRAINT `fk_FieldIntegerValues_FieldsValuesID1` FOREIGN KEY (`field_value_id`) REFERENCES `field_value` (`field_value_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `field_scope`
  ADD CONSTRAINT `fk_FieldsScopes_Group1` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_FieldsScopes_Modules1` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `field_set_values`
  ADD CONSTRAINT `fk_table1_field1` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_table1_field_value1` FOREIGN KEY (`field_value_id`) REFERENCES `field_value` (`field_value_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `field_type`
  ADD CONSTRAINT `fk_FieldsTypes_FieldsTypes1` FOREIGN KEY (`field_type_base_id`) REFERENCES `field_type` (`field_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `field_value`
  ADD CONSTRAINT `fk_FieldsValuesID_Fields1` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `funnel`
  ADD CONSTRAINT `fk_Funnel_FieldsScopes1` FOREIGN KEY (`field_scope_id`) REFERENCES `field_scope` (`field_scope_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `group`
  ADD CONSTRAINT `fk_Group_Group1` FOREIGN KEY (`group_parent_id`) REFERENCES `group` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `group_has_user`
  ADD CONSTRAINT `fk_Group_has_User_Group1` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Group_has_User_User1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `job`
  ADD CONSTRAINT `fk_job_user1` FOREIGN KEY (`job_creator`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `job_ibfk_1` FOREIGN KEY (`job_file`) REFERENCES `field_value` (`field_value_id`),
    ADD CONSTRAINT `job_ibfk_2` FOREIGN KEY (`job_chat_id`) REFERENCES `chat` (`chat_id`);

ALTER TABLE `job_result`
  ADD CONSTRAINT `fk_job_has_user_job1` FOREIGN KEY (`job_id`) REFERENCES `job` (`job_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_job_has_user_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `job_result_ibfk_1` FOREIGN KEY (`job_result_file`) REFERENCES `field_array` (`field_value_parent_id`);

ALTER TABLE `message`
  ADD CONSTRAINT `fk_Message_Chat1` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`chat_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Message_Contact1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Message_FieldsValuesID1` FOREIGN KEY (`message_file`) REFERENCES `field_value` (`field_value_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notification_initiator11` FOREIGN KEY (`initiator_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notification_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `order`
  ADD CONSTRAINT `fk_Order_Chat1` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`chat_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Order_Contact1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Order_Funnel1` FOREIGN KEY (`funnel_id`) REFERENCES `funnel` (`funnel_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_Order_OrderStatuses1` FOREIGN KEY (`order_status_id`) REFERENCES `order_status` (`order_status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Order_creator` FOREIGN KEY (`order_user_creator`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Order_mentor` FOREIGN KEY (`order_user_mentor`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_order_worker` FOREIGN KEY (`order_user_worker`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `order_has_field_value`
  ADD CONSTRAINT `fk_FieldsValuesID_has_Order_FieldsValuesID1` FOREIGN KEY (`field_value_id`) REFERENCES `field_value` (`field_value_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_FieldsValuesID_has_Order_Order1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `order_has_job`
  ADD CONSTRAINT `fk_Order_has_Job_Job1` FOREIGN KEY (`job_id`) REFERENCES `job` (`job_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Order_has_Job_Order1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `order_has_tag`
  ADD CONSTRAINT `fk_Tag_has_Order_Order1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Tag_has_Order_Tag1` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `order_status`
  ADD CONSTRAINT `fk_OrderStatuses_FieldsScopes1` FOREIGN KEY (`field_scope_id`) REFERENCES `field_scope` (`field_scope_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_OrderStatuses_Funnel1` FOREIGN KEY (`funnel_id`) REFERENCES `funnel` (`funnel_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `order_status_history`
  ADD CONSTRAINT `fk_Order_has_OrderStatuses_Order1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_Order_has_OrderStatuses_OrderStatuses1` FOREIGN KEY (`order_status_id`) REFERENCES `order_status` (`order_status_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `session`
  ADD CONSTRAINT `fk_session_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `user`
  ADD CONSTRAINT `fk_User_Contact1` FOREIGN KEY (`user_contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_role1` FOREIGN KEY (`user_role_id`) REFERENCES `role` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `user_like_contact`
  ADD CONSTRAINT `fk_user_has_contact_contact1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_has_contact_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `user_like_order`
  ADD CONSTRAINT `fk_user_has_order_order1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_has_order_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;


ALTER TABLE `chat_has_chat`
  ADD CONSTRAINT `chat_has_chat_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`chat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_has_chat_ibfk_2` FOREIGN KEY (`parent_chat_id`) REFERENCES `chat` (`chat_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

ALTER TABLE `chat_has_message`
  ADD CONSTRAINT `chat_has_message_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`chat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_has_message_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `message` (`message_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;