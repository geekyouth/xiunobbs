# bbs_attach
TRUNCATE bbs_attach;

# bbs_forum
TRUNCATE bbs_forum;
INSERT INTO bbs_forum SET fid='1', name='默认版块', brief='默认版块介绍';

# bbs_post
TRUNCATE bbs_post;

# bbs_thread
TRUNCATE bbs_thread;

# bbs_well_tag
TRUNCATE bbs_well_tag;

# bbs_well_tag_data
TRUNCATE bbs_well_tag_data;

# bbs_well_thread
TRUNCATE bbs_well_thread;

# bbs_well_thread_flag
TRUNCATE bbs_well_thread_flag