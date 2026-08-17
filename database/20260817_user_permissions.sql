CREATE TABLE IF NOT EXISTS user_permission_tb (
  user_id INT NOT NULL,
  permission_key VARCHAR(64) NOT NULL,
  PRIMARY KEY (user_id, permission_key),
  CONSTRAINT fk_user_permission_user
    FOREIGN KEY (user_id) REFERENCES auth_tb(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
