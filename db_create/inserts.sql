INSERT INTO `field_type` (`field_type_id`, `field_type_name`, `field_type_base_id`) VALUES
(1, 'Int', NULL),
(2, 'Char', NULL),
(3, 'Decimal', NULL),
(4, 'File', NULL),
(5, 'Object/File', NULL),
(6, 'Photo', 4),
(7, 'List', NULL),
(8, 'StaticSet', NULL),
(9, 'DynamicSet', NULL),
(10, 'Date', 1),
(11, 'Colour', 2),
(12, 'Email', 2),
(13, 'Phone', 2),
(14, 'Money', 3),
(15, 'Boolean', 1);


INSERT INTO `group` (`group_id`,`group_name`) VALUES (1,'Главный');
INSERT INTO `module` (`module_id`, `module_name`) VALUES (1, 'Главный');

INSERT INTO `field_scope` (`field_scope_name`, `module_id`,`group_id`) VALUES ('contact', '1','1');
INSERT INTO `field_scope` (`field_scope_name`, `module_id`,`group_id`) VALUES ('order', '1','1');

insert role values(1,"Admin"),(2,"Manager"),("3","User");


INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`) VALUES ('post', 'Должность', '1', '2');
-- INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`) VALUES ('organisation', 'Организация', '1', '2');
INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`, `field_sub_type_id`,`field_max_count`) VALUES ('organisation', 'Организация', '1', '9', '2','1');
INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`, `field_icon_name`,`field_max_count`) VALUES ('phone_number', 'Телефон', '1', '13','phone_number',5);
INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`, `field_icon_name`,`field_max_count`) VALUES ('phone_work_number', 'Телефон рабочий', '1', '13','phone_work_number',5);
INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`, `field_icon_name`,`field_max_count`) VALUES ('email', 'Почта', '1', '12','email',5);
INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`) VALUES ('address', 'Адрес', '1', '2');
INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`) VALUES ('birthday', 'День рождения', '1', '10');
INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`) VALUES ('note', 'Заметка', '1', '2');
INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`) VALUES ('avatar', 'Аватар', '1', '6');

INSERT INTO `field` (`field_name`, `field_view_name`, `field_scope_id`, `field_type_id`, `field_sub_type_id`,`field_min_count`,`field_max_count`) VALUES ('why_delete', 'Причина отмены', '2', '8','2','1','1');
SET @DELETE_REASON_ID = LAST_INSERT_ID();


INSERT INTO `field_value` VALUES(1, @DELETE_REASON_ID);
INSERT INTO `field_value` VALUES(2, @DELETE_REASON_ID);
INSERT INTO `field_value` VALUES(3, @DELETE_REASON_ID);
INSERT INTO `field_value` VALUES(4, @DELETE_REASON_ID); 
INSERT INTO `field_value` VALUES(5, @DELETE_REASON_ID);
 
INSERT  field_char_value(field_value_id,field_char_value) VALUES(1,'Без причины');
INSERT  field_char_value(field_value_id,field_char_value) VALUES(2,'Слишком дорого'); 
INSERT  field_char_value(field_value_id,field_char_value) VALUES(3,'Пропала потребность');
INSERT  field_char_value(field_value_id,field_char_value) VALUES(4,'Не устроили условия');
INSERT  field_char_value(field_value_id,field_char_value) VALUES(5,'Выбрали других');

INSERT INTO `funnel` (`funnel_id`, `funnel_name`, `field_scope_id`) VALUES (NULL, 'Воронка', '2');

INSERT INTO `order_status` (`order_status_name`,`order_status_final`,`field_scope_id`,`funnel_id`,`order_status_style`) VALUES ("Cancelled",0,2,1,'ea3a3d'), ("Success",1,2,1,'1ad598'),("Новые",NULL,2,1,'29B6F6'),("В работе",NULL,2,1,'EC407A'),("Принимаю решение",NULL,2,1,'66BB6A');