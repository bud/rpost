-- tabale for followers of users
CREATE TABLE followers (
    follower_id VARCHAR(50),
    following_id VARCHAR(50),
    FOREIGN KEY (follower_id) REFERENCES users(rcsid),
    FOREIGN KEY (following_id) REFERENCES users(rcsid)
);