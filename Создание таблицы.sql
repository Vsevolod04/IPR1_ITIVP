CREATE TABLE print_orders (
	id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(200),
    tel_num VARCHAR(200),
    document_name VARCHAR(200), -- название док-та (file.doc)
    print_format VARCHAR(200), -- A4 цветная / фото 
    copies INT, -- не более 500?
    pickup_date DATE,	-- дата, когда забрать (начиная с завтра)
    created_at DATETIME DEFAULT current_timestamp
);

INSERT print_orders (customer_name, tel_num, document_name, print_format, copies, pickup_date)
VALUES (
"Тестовый заказчик",
"+375293086885",
"1.txt",
"A4 ЧБ",
1,
"2025-09-29"
);
