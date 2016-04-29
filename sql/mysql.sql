CREATE TABLE `sl_recoder` (
`r_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `UserID` varchar(50) DEFAULT NULL,
  `UserName` varchar(20) DEFAULT NULL,
  `Email` text,
  `student_id` varchar(20) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `desc` text,
  `class` varchar(30) DEFAULT NULL,
  `class_number` int(11) DEFAULT NULL,
  `r_note` text,
  `r_time` text,
  PRIMARY KEY (`r_id`)
)ENGINE=MyISAM;

CREATE TABLE `sl_task` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_title` text NOT NULL COMMENT '標題',
  `task_desc` text COMMENT '詳細說明',
  `task_worktime` text COMMENT '工作時間',
  `task_limit` text COMMENT '條件限制',
  `task_gather` text COMMENT '集合資訊(含要攜帶的物品)',
  `task_regtime` int(11) DEFAULT NULL COMMENT '可登錄時數',
  `task_maxnum` int(11) DEFAULT NULL COMMENT '需求人數',
  `task_maxnum_enable` int(11) DEFAULT NULL COMMENT '是否限制人數',
  `task_maxnum_each` int(11) DEFAULT NULL COMMENT '每位教師可推薦人數(空白代表不限制)',
  `task_start_line` datetime DEFAULT NULL COMMENT '開始日期與時間',
  `task_dead_line` datetime DEFAULT NULL COMMENT '結束日期與時間',
  `hidden_at_dead_line` int(11) DEFAULT NULL COMMENT '招募期限到達時，是否持續公布於首頁',
  `UserID` varchar(50) DEFAULT NULL,
  `UserName` varchar(12) DEFAULT NULL,
  `Roles` varchar(20) DEFAULT NULL,
  `Email` text,
  `task_enable` int(11) DEFAULT NULL COMMENT '募集中或停止募集',
  `note` text COMMENT '備註(含聯絡方式)',
  `teacher_enable` int(11) DEFAULT NULL COMMENT '開放教師推薦',
  PRIMARY KEY (`task_id`)
)ENGINE=MyISAM;