CREATE TABLE dues_alternative_payments (
                                           id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
                                           uid INT NOT NULL,
                                           alternative_payment_reason varchar(50) NOT NULL,
                                           requested_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                                           requested_term INT(1) NOT NULL,
                                           requested_year INT(4) NOT NULL,
                                           approval_status INT(1) NOT NULL DEFAULT 0,
                                           dues_payment INT,
                                           resolved_timestamp TIMESTAMP NULL DEFAULT NULL,
                                           resolved_by INT,
                                           FOREIGN KEY (uid) REFERENCES users(id),
                                           FOREIGN KEY (resolved_by) REFERENCES users(id),
                                           FOREIGN KEY (dues_payment) REFERENCES dues_payments(id)
)