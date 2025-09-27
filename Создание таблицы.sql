CREATE TABLE print_orders (
	id INT PRIMARY KEY auto_increment,
    customer_name VARCHAR(200),
    tel_num VARCHAR(200),
    document_name VARCHAR(200), -- название док-та (file.doc)
    print_format VARCHAR(200), -- A4 цветная / фото 
    copies INT, -- не более 500?
    pickup_date DATE,	-- дата, когда забрать (начиная с завтра)
    created_at DATETIME DEFAULT current_timestamp
);


