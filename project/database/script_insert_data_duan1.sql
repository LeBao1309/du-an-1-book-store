    USE du_an_1_book_store;

    SET FOREIGN_KEY_CHECKS = 0;

    TRUNCATE TABLE order_items;
    TRUNCATE TABLE payment;
    TRUNCATE TABLE orders;

    TRUNCATE TABLE wishlist;
    TRUNCATE TABLE comments;

    TRUNCATE TABLE user_address;
    TRUNCATE TABLE users;

    TRUNCATE TABLE book_images;
    TRUNCATE TABLE book_variants;
    TRUNCATE TABLE book_publisher;
    TRUNCATE TABLE book_authors;
    TRUNCATE TABLE books;

    TRUNCATE TABLE authors;
    TRUNCATE TABLE publisher;
    TRUNCATE TABLE categories;

    SET FOREIGN_KEY_CHECKS = 1;


    INSERT INTO categories (id, name, slug, parent_id) VALUES
                                                        (1, 'Sách Khoa học', 'sach-khoa-hoc', NULL),
                                                        (2, 'Sách Văn học', 'sach-van-hoc', NULL),
                                                        (3, 'Sách Kinh tế', 'sach-kinh-te', NULL),
                                                        (4, 'Sách Kỹ năng sống', 'sach-ky-nang-song', NULL),
                                                        (5, 'Sách Thiếu nhi', 'sach-thieu-nhi', NULL),
                                                        (6,  'Văn học Việt Nam',     'van-hoc-viet-nam',      2),
                                                        (7,  'Văn học nước ngoài',   'van-hoc-nuoc-ngoai',    2),
                                                        (8,  'Vũ trụ - Vật lý',      'vu-tru-vat-ly',         1),
                                                        (9,  'Sinh học - Tiến hóa',  'sinh-hoc-tien-hoa',     1),
                                                        (10, 'Kinh tế học',          'kinh-te-hoc',           3),
                                                        (11, 'Quản trị - Chiến lược','quan-tri-chien-luoc',   3),
                                                        (12, 'Tài chính cá nhân',    'tai-chinh-ca-nhan',     3),
                                                        (13, 'Self-help kinh điển',  'self-help-kinh-dien',   4),
                                                        (14, 'Tư duy - Thói quen',   'tu-duy-thoi-quen',      4),
                                                        (15, 'Năng suất - Deep Work','nang-suat-deep-work',   4),
                                                        (16, 'Truyện thiếu nhi kinh điển', 'truyen-thieu-nhi-kinh-dien', 5),
                                                        (17, 'Văn học tuổi mới lớn',       'van-hoc-tuoi-moi-lon',       5);

    INSERT INTO authors (id, name, slug) VALUES
                                            (1, 'George Orwell', 'george-orwell'),
                                            (2, 'Stephen R. Covey', 'stephen-r-covey'),
                                            (3, 'L. M. Montgomery', 'l-m-montgomery'),
                                            (4, 'James Clear', 'james-clear'),
                                            (5, 'Tony Buzan', 'tony-buzan'),
                                            (6, 'T. Harv Eker', 't-harv-eker'),
                                            (7, 'Haruki Murakami', 'haruki-murakami'),
                                            (8, 'Mario Puzo', 'mario-puzo'),
                                            (9, 'Robert T. Kiyosaki', 'robert-t-kiyosaki'),
                                            (10, 'Nam Cao', 'nam-cao'),
                                            (11, 'W. Chan Kim & Renée Mauborgne', 'kim-mauborgne'),
                                            (12, 'Nguyễn Nhật Ánh', 'nguyen-nhat-anh'),
                                            (13, 'John Boyne', 'john-boyne'),
                                            (14, 'Robert C. Martin', 'robert-c-martin'),
                                            (15, 'Louise Hay', 'louise-hay'),
                                            (16, 'Margaret Mitchell', 'margaret-mitchell'),
                                            (17, 'Dale Carnegie', 'dale-carnegie'),
                                            (18, 'Nhiều tác giả', 'nhieu-tac-gia'),
                                            (19, 'Ichiro Kishimi & Fumitake Koga', 'ichiro-kishimi-fumitake-koga'),
                                            (20, 'Tô Hoài', 'to-hoai'),
                                            (21, 'Cal Newport', 'cal-newport'),
                                            (22, 'Joseph Murphy', 'joseph-murphy'),
                                            (23, 'Nguyễn Ngọc Tư', 'nguyen-ngoc-tu'),
                                            (24, 'Tara Westover', 'tara-westover'),
                                            (25, 'Marijn Haverbeke', 'marijn-haverbeke'),
                                            (26, 'Greg McKeown', 'greg-mckeown'),
                                            (27, 'Hans Rosling', 'hans-rosling'),
                                            (28, 'Mihaly Csikszentmihalyi', 'mihaly-csikszentmihalyi'),
                                            (29, 'Steven Levitt & Stephen Dubner', 'steven-levitt-stephen-dubner'),
                                            (30, 'Siddhartha Mukherjee', 'siddhartha-mukherjee'),
                                            (31, 'Harper Lee', 'harper-lee'),
                                            (32, 'Fyodor Dostoevsky', 'fyodor-dostoevsky'),
                                            (33, 'J.K. Rowling', 'jk-rowling'),
                                            (34, 'Antoine de Saint-Exupéry', 'antoine-de-saint-exupery'),
                                            (35, 'Barbara Oakley', 'barbara-oakley'),
                                            (36, 'Yuval Noah Harari', 'yuval-noah-harari'),
                                            (37, 'Nir Eyal', 'nir-eyal'),
                                            (38, 'Héctor García & Francesc Miralles', 'hector-garcia-francesc-miralles'),
                                            (39, 'Hector Malot', 'hector-malot'),
                                            (40, 'Howard Zinn', 'howard-zinn'),
                                            (41, 'Franz Kafka', 'franz-kafka'),
                                            (42, 'Stephen Hawking', 'stephen-hawking'),
                                            (43, 'Jake Knapp & John Zeratsky', 'jake-knapp-john-zeratsky'),
                                            (44, 'Viktor E. Frankl', 'viktor-e-frankl'),
                                            (45, 'Fumio Sasaki', 'fumio-sasaki'),
                                            (46, 'Charles Darwin', 'charles-darwin'),
                                            (47, 'Paulo Coelho', 'paulo-coelho'),
                                            (48, 'Robin Sharma', 'robin-sharma'),
                                            (49, 'Victor Hugo', 'victor-hugo'),
                                            (50, 'Robert Greene', 'robert-greene'),
                                            (51, 'Ernest Hemingway', 'ernest-hemingway'),
                                            (52, 'Jonas Jonasson', 'jonas-jonasson'),
                                            (53, 'Malcolm Gladwell', 'malcolm-gladwell'),
                                            (54, 'Susan Cain', 'susan-cain'),
                                            (55, 'Jason Fried & David Heinemeier Hansson', 'jason-fried-dhh'),
                                            (56, 'Vũ Trọng Phụng', 'vu-trong-phung'),
                                            (57, 'Simon Sinek', 'simon-sinek'),
                                            (58, 'Jack Canfield et al.', 'jack-canfield-et-al'),
                                            (59, 'Jared Diamond', 'jared-diamond'),
                                            (60, 'Gustave Le Bon', 'gustave-le-bon'),
                                            (61, 'Eric Ries', 'eric-ries'),
                                            (62, 'Andrew Hunt & David Thomas', 'andrew-hunt-david-thomas'),
                                            (63, 'Robert M. Pirsig', 'robert-pirsig'),
                                            (64, 'Daniel Kahneman', 'daniel-kahneman'),
                                            (65, 'Tetsuko Kuroyanagi', 'tetsuko-kuroyanagi'),
                                            (66, 'Gabriel García Márquez', 'gabriel-garcia-marquez'),
                                            (67, 'Daniel Goleman', 'daniel-goleman'),
                                            (68, 'Nguyễn Du', 'nguyen-du'),
                                            (69, 'Napoleon Hill', 'napoleon-hill'),
                                            (70, 'Jim Collins', 'jim-collins'),
                                            (71, 'Tim Ferriss', 'tim-ferriss'),
                                            (72, 'Michio Kaku', 'michio-kaku'),
                                            (73, 'Trần Trọng Kim', 'tran-trong-kim'),
                                            (74, 'Peter Thiel', 'peter-thiel');

    INSERT INTO publisher (id, name, slug) VALUES
                                            (1, 'NXB Trẻ', 'nxb-tre'),
                                            (2, 'NXB Kim Đồng', 'nxb-kim-dong'),
                                            (3, 'NXB Lao Động', 'nxb-lao-dong'),
                                            (4, 'NXB Nhã Nam', 'nxb-nha-nam'),
                                            (5, 'NXB Tổng Hợp TP.HCM', 'nxb-tong-hop-tphcm'),
                                            (6, 'NXB Khoa Học & Kỹ Thuật', 'nxb-khoa-hoc-ky-thuat');

    INSERT INTO books (id, title, slug, category_id, description, short_desc, rating_avg, review_count, is_active, created_at, updated_at) VALUES
                                                                                                                                            (1, '1984', '1984', 2, 'Sample description for 1984.', '1984 — sample short description.', 4.60, 850, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (2, '7 Thoi Quen', '7-thoi-quen', 4, 'Sample description for 7 Thoi Quen.', '7 Thoi Quen — sample short description.', 4.70, 900, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (3, 'Anne Toc Do Duoi Chai Nha Xanh', 'anne-toc-do-duoi-chai-nha-xanh', 5, 'Sample description for Anne Toc Do Duoi Chai Nha Xanh.', 'Anne Toc Do Duoi Chai Nha Xanh — sample short description.', 4.80, 950, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (4, 'Atomic Habits', 'atomic-habits', 4, 'Sample description for Atomic Habits.', 'Atomic Habits — sample short description.', 4.90, 1000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (5, 'Ban Do Tu Duy', 'ban-do-tu-duy', 3, 'Sample description for Ban Do Tu Duy.', 'Ban Do Tu Duy — sample short description.', 4.50, 1050, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (6, 'Bi Mat Tu Duy Trieu Phu', 'bi-mat-tu-duy-trieu-phu', 3, 'Sample description for Bi Mat Tu Duy Trieu Phu.', 'Bi Mat Tu Duy Trieu Phu — sample short description.', 4.60, 1100, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (7, 'Bien Nien Ky Chim Van Day Cot', 'bien-nien-ky-chim-van-day-cot', 2, 'Sample description for Bien Nien Ky Chim Van Day Cot.', 'Bien Nien Ky Chim Van Day Cot — sample short description.', 4.70, 1150, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (8, 'Bo Gia', 'bo-gia', 2, 'Sample description for Bo Gia.', 'Bo Gia — sample short description.', 4.80, 1200, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (9, 'Cha Giau Cha Ngheo', 'cha-giau-cha-ngheo', 3, 'Sample description for Cha Giau Cha Ngheo.', 'Cha Giau Cha Ngheo — sample short description.', 4.90, 1250, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (10, 'Chi Pheo', 'chi-pheo', 2, 'Sample description for Chi Pheo.', 'Chi Pheo — sample short description.', 4.50, 1300, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (11, 'Chien Luoc Dai Duong Xanh', 'chien-luoc-dai-duong-xanh', 3, 'Sample description for Chien Luoc Dai Duong Xanh.', 'Chien Luoc Dai Duong Xanh — sample short description.', 4.60, 1350, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (12, 'Cho Toi Xin Mot Ve Di Tuoi Tho', 'cho-toi-xin-mot-ve-di-tuoi-tho', 2, 'Sample description for Cho Toi Xin Mot Ve Di Tuoi Tho.', 'Cho Toi Xin Mot Ve Di Tuoi Tho — sample short description.', 4.70, 1400, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (13, 'Chu Be Mang Pyjama Soc', 'chu-be-mang-pyjama-soc', 2, 'Sample description for Chu Be Mang Pyjama Soc.', 'Chu Be Mang Pyjama Soc — sample short description.', 4.80, 1450, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (14, 'Clean Code', 'clean-code', 2, 'Sample description for Clean Code.', 'Clean Code — sample short description.', 4.90, 1500, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (15, 'Co The Tu Chua Lanh', 'co-the-tu-chua-lanh', 4, 'Sample description for Co The Tu Chua Lanh.', 'Co The Tu Chua Lanh — sample short description.', 4.50, 1550, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (16, 'Cuon Theo Chieu Gio', 'cuon-theo-chieu-gio', 2, 'Sample description for Cuon Theo Chieu Gio.', 'Cuon Theo Chieu Gio — sample short description.', 4.60, 1600, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (17, 'Dac Nhan Tam', 'dac-nhan-tam', 2, 'Sample description for Dac Nhan Tam.', 'Dac Nhan Tam — sample short description.', 4.70, 1650, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (18, 'Dai Viet Su Ky Toan Thu', 'dai-viet-su-ky-toan-thu', 1, 'Sample description for Dai Viet Su Ky Toan Thu.', 'Dai Viet Su Ky Toan Thu — sample short description.', 4.80, 1700, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (19, 'Dam Bi Ghet', 'dam-bi-ghet', 2, 'Sample description for Dam Bi Ghet.', 'Dam Bi Ghet — sample short description.', 4.90, 1750, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (20, 'De Men Phieu Luu Ky', 'de-men-phieu-luu-ky', 5, 'Sample description for De Men Phieu Luu Ky.', 'De Men Phieu Luu Ky — sample short description.', 4.50, 1800, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (21, 'Deep Work', 'deep-work', 4, 'Sample description for Deep Work.', 'Deep Work — sample short description.', 4.60, 1850, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (22, 'Di Tim Le Song', 'di-tim-le-song', 4, 'Sample description for Di Tim Le Song.', 'Di Tim Le Song — sample short description.', 4.70, 1900, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (23, 'Dieu Ky Dieu Cua Tiem Thuc', 'dieu-ky-dieu-cua-tiem-thuc', 4, 'Sample description for Dieu Ky Dieu Cua Tiem Thuc.', 'Dieu Ky Dieu Cua Tiem Thuc — sample short description.', 4.80, 1950, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (24, 'Digital Minimalism', 'digital-minimalism', 2, 'Sample description for Digital Minimalism.', 'Digital Minimalism — sample short description.', 4.90, 2000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (25, 'Doi Gio Hu', 'doi-gio-hu', 2, 'Sample description for Doi Gio Hu.', 'Doi Gio Hu — sample short description.', 4.50, 2050, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (26, 'Educated', 'educated', 2, 'Sample description for Educated.', 'Educated — sample short description.', 4.60, 2100, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (27, 'Eloquent Javascript', 'eloquent-javascript', 2, 'Sample description for Eloquent Javascript.', 'Eloquent Javascript — sample short description.', 4.70, 2150, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (28, 'Essentialism', 'essentialism', 4, 'Sample description for Essentialism.', 'Essentialism — sample short description.', 4.80, 2200, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (29, 'Factfulness', 'factfulness', 1, 'Sample description for Factfulness.', 'Factfulness — sample short description.', 4.90, 2250, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (30, 'Flow Dong Chay', 'flow-dong-chay', 4, 'Sample description for Flow Dong Chay.', 'Flow Dong Chay — sample short description.', 4.50, 2300, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (31, 'Freakonomics', 'freakonomics', 3, 'Sample description for Freakonomics.', 'Freakonomics — sample short description.', 4.60, 2350, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (32, 'Gene Lich Su Va Tuong Lai', 'gene-lich-su-va-tuong-lai', 1, 'Sample description for Gene Lich Su Va Tuong Lai.', 'Gene Lich Su Va Tuong Lai — sample short description.', 4.70, 2400, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (33, 'Giet Con Chim Nhai', 'giet-con-chim-nhai', 2, 'Sample description for Giet Con Chim Nhai.', 'Giet Con Chim Nhai — sample short description.', 4.80, 2450, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (34, 'Hai So Phan', 'hai-so-phan', 2, 'Sample description for Hai So Phan.', 'Hai So Phan — sample short description.', 4.90, 2500, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (35, 'Harry Potter Va Hon Da Phu Thuy', 'harry-potter-va-hon-da-phu-thuy', 5, 'Sample description for Harry Potter Va Hon Da Phu Thuy.', 'Harry Potter Va Hon Da Phu Thuy — sample short description.', 4.50, 2550, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (36, 'Harry Potter Va Phong Chua Bi Mat', 'harry-potter-va-phong-chua-bi-mat', 5, 'Sample description for Harry Potter Va Phong Chua Bi Mat.', 'Harry Potter Va Phong Chua Bi Mat — sample short description.', 4.60, 2600, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (37, 'Hoang Tu Be', 'hoang-tu-be', 5, 'Sample description for Hoang Tu Be.', 'Hoang Tu Be — sample short description.', 4.70, 2650, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (38, 'Hoang Tu Va Nguoi An May', 'hoang-tu-va-nguoi-an-may', 5, 'Sample description for Hoang Tu Va Nguoi An May.', 'Hoang Tu Va Nguoi An May — sample short description.', 4.80, 2700, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (39, 'Hoc Cach Hoc', 'hoc-cach-hoc', 4, 'Sample description for Hoc Cach Hoc.', 'Hoc Cach Hoc — sample short description.', 4.90, 2750, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (40, 'Homo Deus Luoc Su Tuong Lai', 'homo-deus-luoc-su-tuong-lai', 1, 'Sample description for Homo Deus Luoc Su Tuong Lai.', 'Homo Deus Luoc Su Tuong Lai — sample short description.', 4.50, 2800, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (41, 'Hooked', 'hooked', 4, 'Sample description for Hooked.', 'Hooked — sample short description.', 4.60, 2850, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (42, 'Ikigai', 'ikigai', 4, 'Sample description for Ikigai.', 'Ikigai — sample short description.', 4.70, 2900, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (43, 'Kafka Ben Bo Bien', 'kafka-ben-bo-bien', 2, 'Sample description for Kafka Ben Bo Bien.', 'Kafka Ben Bo Bien — sample short description.', 4.80, 2950, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (44, 'Khong Gia Dinh', 'khong-gia-dinh', 5, 'Sample description for Khong Gia Dinh.', 'Khong Gia Dinh — sample short description.', 4.90, 3000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (45, 'Kinh Te Hoc Hai Huoc', 'kinh-te-hoc-hai-huoc', 3, 'Sample description for Kinh Te Hoc Hai Huoc.', 'Kinh Te Hoc Hai Huoc — sample short description.', 4.50, 3050, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (46, 'Lich Su My', 'lich-su-my', 1, 'Sample description for Lich Su My.', 'Lich Su My — sample short description.', 4.60, 3100, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (47, 'Loi Thu Toi', 'loi-thu-toi', 2, 'Sample description for Loi Thu Toi.', 'Loi Thu Toi — sample short description.', 4.70, 3150, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (48, 'Luoc Su Thoi Gian', 'luoc-su-thoi-gian', 1, 'Sample description for Luoc Su Thoi Gian.', 'Luoc Su Thoi Gian — sample short description.', 4.80, 3200, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (49, 'Make Time', 'make-time', 4, 'Sample description for Make Time.', 'Make Time — sample short description.', 4.90, 3250, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (50, 'Mans Search For Meanin', 'mans-search-for-meanin', 4, 'Sample description for Mans Search For Meanin.', 'Mans Search For Meanin — sample short description.', 4.50, 3300, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (51, 'Mat Biec', 'mat-biec', 2, 'Sample description for Mat Biec.', 'Mat Biec — sample short description.', 4.60, 3350, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (52, 'Nghe Thuat Noi Truoc Cong Chung', 'nghe-thuat-noi-truoc-cong-chung', 4, 'Sample description for Nghe Thuat Noi Truoc Cong Chung.', 'Nghe Thuat Noi Truoc Cong Chung — sample short description.', 4.70, 3400, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (53, 'Nghe Thuat Song Toi Gian', 'nghe-thuat-song-toi-gian', 4, 'Sample description for Nghe Thuat Song Toi Gian.', 'Nghe Thuat Song Toi Gian — sample short description.', 4.80, 3450, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (54, 'Nguon Goc Cac Loai', 'nguon-goc-cac-loai', 1, 'Sample description for Nguon Goc Cac Loai.', 'Nguon Goc Cac Loai — sample short description.', 4.90, 3500, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (55, 'Nha Gia Kim', 'nha-gia-kim', 2, 'Sample description for Nha Gia Kim.', 'Nha Gia Kim — sample short description.', 4.50, 3550, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (56, 'Nha Lanh Dao Khong Chuc Danh', 'nha-lanh-dao-khong-chuc-danh', 4, 'Sample description for Nha Lanh Dao Khong Chuc Danh.', 'Nha Lanh Dao Khong Chuc Danh — sample short description.', 4.60, 3600, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (57, 'Nhung Cuoc Thap Tu Chinh', 'nhung-cuoc-thap-tu-chinh', 2, 'Sample description for Nhung Cuoc Thap Tu Chinh.', 'Nhung Cuoc Thap Tu Chinh — sample short description.', 4.70, 3650, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (58, 'Nhung Nguoi Khon Kho', 'nhung-nguoi-khon-kho', 2, 'Sample description for Nhung Nguoi Khon Kho.', 'Nhung Nguoi Khon Kho — sample short description.', 4.80, 3700, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (59, 'Nhung Quy Luat Cua Ban Chat Con Nguoi', 'nhung-quy-luat-cua-ban-chat-con-nguoi', 2, 'Sample description for Nhung Quy Luat Cua Ban Chat Con Nguoi.', 'Nhung Quy Luat Cua Ban Chat Con Nguoi — sample short description.', 4.90, 3750, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (60, 'Nhung Tu Nhan Dia Ly', 'nhung-tu-nhan-dia-ly', 1, 'Sample description for Nhung Tu Nhan Dia Ly.', 'Nhung Tu Nhan Dia Ly — sample short description.', 4.50, 3800, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (61, 'Norwegian Wood Ban Tieng Anh', 'norwegian-wood-ban-tieng-anh', 2, 'Sample description for Norwegian Wood Ban Tieng Anh.', 'Norwegian Wood Ban Tieng Anh — sample short description.', 4.60, 3850, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (62, 'Ong Gia Va Bien Ca', 'ong-gia-va-bien-ca', 2, 'Sample description for Ong Gia Va Bien Ca.', 'Ong Gia Va Bien Ca — sample short description.', 4.70, 3900, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (63, 'Ong Tram Tuoi Treo Qua Cua So Va Bien Mat', 'ong-tram-tuoi-treo-qua-cua-so-va-bien-mat', 2, 'Sample description for Ong Tram Tuoi Treo Qua Cua So Va Bien Mat.', 'Ong Tram Tuoi Treo Qua Cua So Va Bien Mat — sample short description.', 4.80, 3950, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (64, 'Outliers', 'outliers', 3, 'Sample description for Outliers.', 'Outliers — sample short description.', 4.90, 4000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (65, 'Quiet', 'quiet', 4, 'Sample description for Quiet.', 'Quiet — sample short description.', 4.50, 4050, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (66, 'Rework', 'rework', 4, 'Sample description for Rework.', 'Rework — sample short description.', 4.60, 4100, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (67, 'Rung Na Uy', 'rung-na-uy', 2, 'Sample description for Rung Na Uy.', 'Rung Na Uy — sample short description.', 4.70, 4150, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (68, 'Sapiens Luoc Su Loai Nguoi', 'sapiens-luoc-su-loai-nguoi', 1, 'Sample description for Sapiens Luoc Su Loai Nguoi.', 'Sapiens Luoc Su Loai Nguoi — sample short description.', 4.80, 4200, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (69, 'So Do', 'so-do', 2, 'Sample description for So Do.', 'So Do — sample short description.', 4.90, 4250, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (70, 'Song Doi Tu Do', 'song-doi-tu-do', 4, 'Sample description for Song Doi Tu Do.', 'Song Doi Tu Do — sample short description.', 4.50, 4300, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (71, 'Start With Why', 'start-with-why', 4, 'Sample description for Start With Why.', 'Start With Why — sample short description.', 4.60, 4350, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (72, 'Suc Manh Cua Su Tap Trung', 'suc-manh-cua-su-tap-trung', 4, 'Sample description for Suc Manh Cua Su Tap Trung.', 'Suc Manh Cua Su Tap Trung — sample short description.', 4.70, 4400, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (73, 'Sung Vi Trung Va Thep', 'sung-vi-trung-va-thep', 1, 'Sample description for Sung Vi Trung Va Thep.', 'Sung Vi Trung Va Thep — sample short description.', 4.80, 4450, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (74, 'Tam Ly Hoc Dam Dong', 'tam-ly-hoc-dam-dong', 4, 'Sample description for Tam Ly Hoc Dam Dong.', 'Tam Ly Hoc Dam Dong — sample short description.', 4.90, 4500, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (75, 'The Lean Startup', 'the-lean-startup', 3, 'Sample description for The Lean Startup.', 'The Lean Startup — sample short description.', 4.50, 4550, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (76, 'The Pragmatic Programmer', 'the-pragmatic-programmer', 2, 'Sample description for The Pragmatic Programmer.', 'The Pragmatic Programmer — sample short description.', 4.60, 4600, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (77, 'Thien Tai Ben Trong', 'thien-tai-ben-trong', 4, 'Sample description for Thien Tai Ben Trong.', 'Thien Tai Ben Trong — sample short description.', 4.70, 4650, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (78, 'Thien Va Nghe Thuat Bao Duong Xe May', 'thien-va-nghe-thuat-bao-duong-xe-may', 4, 'Sample description for Thien Va Nghe Thuat Bao Duong Xe May.', 'Thien Va Nghe Thuat Bao Duong Xe May — sample short description.', 4.80, 4700, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (79, 'Thinking Fast And Slow English', 'thinking-fast-and-slow-english', 2, 'Sample description for Thinking Fast And Slow English.', 'Thinking Fast And Slow English — sample short description.', 4.90, 4750, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (80, 'Toi Ac Va Trung Phat', 'toi-ac-va-trung-phat', 2, 'Sample description for Toi Ac Va Trung Phat.', 'Toi Ac Va Trung Phat — sample short description.', 4.50, 4800, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (81, 'Totto Chan Ben Cua So', 'totto-chan-ben-cua-so', 5, 'Sample description for Totto Chan Ben Cua So.', 'Totto Chan Ben Cua So — sample short description.', 4.60, 4850, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (82, 'Tram Nam Co Don', 'tram-nam-co-don', 2, 'Sample description for Tram Nam Co Don.', 'Tram Nam Co Don — sample short description.', 4.70, 4900, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (83, 'Tri Tue Cam Xuc', 'tri-tue-cam-xuc', 4, 'Sample description for Tri Tue Cam Xuc.', 'Tri Tue Cam Xuc — sample short description.', 4.80, 4950, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (84, 'Truyen Kieu', 'truyen-kieu', 2, 'Sample description for Truyen Kieu.', 'Truyen Kieu — sample short description.', 4.90, 5000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (85, 'Tu Duy Lam Giau', 'tu-duy-lam-giau', 4, 'Sample description for Tu Duy Lam Giau.', 'Tu Duy Lam Giau — sample short description.', 4.50, 5050, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (86, 'Tu Duy Nhanh Va Cham', 'tu-duy-nhanh-va-cham', 2, 'Sample description for Tu Duy Nhanh Va Cham.', 'Tu Duy Nhanh Va Cham — sample short description.', 4.60, 5100, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (87, 'Tu Duy Phan Bien', 'tu-duy-phan-bien', 4, 'Sample description for Tu Duy Phan Bien.', 'Tu Duy Phan Bien — sample short description.', 4.70, 5150, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (88, 'Tu Tot Den Vi Dai', 'tu-tot-den-vi-dai', 4, 'Sample description for Tu Tot Den Vi Dai.', 'Tu Tot Den Vi Dai — sample short description.', 4.80, 5200, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (89, 'Tuan Lam Viec 4 Gio', 'tuan-lam-viec-4-gio', 3, 'Sample description for Tuan Lam Viec 4 Gio.', 'Tuan Lam Viec 4 Gio — sample short description.', 4.90, 5250, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (90, 'Vat Ly Cua Tuong Lai', 'vat-ly-cua-tuong-lai', 1, 'Sample description for Vat Ly Cua Tuong Lai.', 'Vat Ly Cua Tuong Lai — sample short description.', 4.50, 5300, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (91, 'Viet Nam Su Luoc', 'viet-nam-su-luoc', 1, 'Sample description for Viet Nam Su Luoc.', 'Viet Nam Su Luoc — sample short description.', 4.60, 5350, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (92, 'Vo Chong A Phu', 'vo-chong-a-phu', 2, 'Sample description for Vo Chong A Phu.', 'Vo Chong A Phu — sample short description.', 4.70, 5400, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (93, 'Vu Tru Trong Vo Hat De', 'vu-tru-trong-vo-hat-de', 1, 'Sample description for Vu Tru Trong Vo Hat De.', 'Vu Tru Trong Vo Hat De — sample short description.', 4.80, 5450, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (94, 'Vua Nham Mat Vua Mo Cua So', 'vua-nham-mat-vua-mo-cua-so', 5, 'Sample description for Vua Nham Mat Vua Mo Cua So.', 'Vua Nham Mat Vua Mo Cua So — sample short description.', 4.90, 5500, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                            (95, 'Zero To One', 'zero-to-one', 3, 'Sample description for Zero To One.', 'Zero To One — sample short description.', 4.50, 5550, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56');

    INSERT INTO book_authors (id, book_id, author_id) VALUES
                                                        (1, 1, 1),
                                                        (2, 2, 2),
                                                        (3, 3, 3),
                                                        (4, 4, 4),
                                                        (5, 5, 5),
                                                        (6, 6, 6),
                                                        (7, 7, 7),
                                                        (8, 8, 8),
                                                        (9, 9, 9),
                                                        (10, 10, 10),
                                                        (11, 11, 11),
                                                        (12, 12, 12),
                                                        (13, 13, 13),
                                                        (14, 14, 14),
                                                        (15, 15, 15),
                                                        (16, 16, 16),
                                                        (17, 17, 17),
                                                        (18, 18, 18),
                                                        (19, 19, 19),
                                                        (20, 20, 20),
                                                        (21, 21, 21),
                                                        (22, 22, 18),
                                                        (23, 23, 22),
                                                        (24, 24, 21),
                                                        (25, 25, 23),
                                                        (26, 26, 24),
                                                        (27, 27, 25),
                                                        (28, 28, 26),
                                                        (29, 29, 27),
                                                        (30, 30, 28),
                                                        (31, 31, 29),
                                                        (32, 32, 30),
                                                        (33, 33, 31),
                                                        (34, 34, 32),
                                                        (35, 35, 33),
                                                        (36, 36, 33),
                                                        (37, 37, 34),
                                                        (38, 38, 18),
                                                        (39, 39, 35),
                                                        (40, 40, 36),
                                                        (41, 41, 37),
                                                        (42, 42, 38),
                                                        (43, 43, 7),
                                                        (44, 44, 39),
                                                        (45, 45, 29),
                                                        (46, 46, 40),
                                                        (47, 47, 41),
                                                        (48, 48, 42),
                                                        (49, 49, 43),
                                                        (50, 50, 44),
                                                        (51, 51, 12),
                                                        (52, 52, 17),
                                                        (53, 53, 45),
                                                        (54, 54, 46),
                                                        (55, 55, 47),
                                                        (56, 56, 48),
                                                        (57, 57, 18),
                                                        (58, 58, 49),
                                                        (59, 59, 50),
                                                        (60, 60, 18),
                                                        (61, 61, 7),
                                                        (62, 62, 51),
                                                        (63, 63, 52),
                                                        (64, 64, 53),
                                                        (65, 65, 54),
                                                        (66, 66, 55),
                                                        (67, 67, 7),
                                                        (68, 68, 36),
                                                        (69, 69, 56),
                                                        (70, 70, 18),
                                                        (71, 71, 57),
                                                        (72, 72, 58),
                                                        (73, 73, 59),
                                                        (74, 74, 60),
                                                        (75, 75, 61),
                                                        (76, 76, 62),
                                                        (77, 77, 18),
                                                        (78, 78, 63),
                                                        (79, 79, 64),
                                                        (80, 80, 32),
                                                        (81, 81, 65),
                                                        (82, 82, 66),
                                                        (83, 83, 67),
                                                        (84, 84, 68),
                                                        (85, 85, 69),
                                                        (86, 86, 64),
                                                        (87, 87, 18),
                                                        (88, 88, 70),
                                                        (89, 89, 71),
                                                        (90, 90, 72),
                                                        (91, 91, 73),
                                                        (92, 92, 20),
                                                        (93, 93, 42),
                                                        (94, 94, 12),
                                                        (95, 95, 74);

    INSERT INTO book_publisher (id, book_id, publisher_id) VALUES
                                                            (1, 1, 4),
                                                            (2, 2, 4),
                                                            (3, 3, 2),
                                                            (4, 4, 4),
                                                            (5, 5, 4),
                                                            (6, 6, 4),
                                                            (7, 7, 4),
                                                            (8, 8, 4),
                                                            (9, 9, 4),
                                                            (10, 10, 1),
                                                            (11, 11, 4),
                                                            (12, 12, 4),
                                                            (13, 13, 4),
                                                            (14, 14, 6),
                                                            (15, 15, 4),
                                                            (16, 16, 4),
                                                            (17, 17, 4),
                                                            (18, 18, 6),
                                                            (19, 19, 4),
                                                            (20, 20, 2),
                                                            (21, 21, 4),
                                                            (22, 22, 4),
                                                            (23, 23, 4),
                                                            (24, 24, 4),
                                                            (25, 25, 1),
                                                            (26, 26, 4),
                                                            (27, 27, 6),
                                                            (28, 28, 4),
                                                            (29, 29, 6),
                                                            (30, 30, 4),
                                                            (31, 31, 4),
                                                            (32, 32, 6),
                                                            (33, 33, 4),
                                                            (34, 34, 4),
                                                            (35, 35, 2),
                                                            (36, 36, 2),
                                                            (37, 37, 2),
                                                            (38, 38, 2),
                                                            (39, 39, 4),
                                                            (40, 40, 6),
                                                            (41, 41, 4),
                                                            (42, 42, 4),
                                                            (43, 43, 4),
                                                            (44, 44, 2),
                                                            (45, 45, 4),
                                                            (46, 46, 6),
                                                            (47, 47, 4),
                                                            (48, 48, 6),
                                                            (49, 49, 4),
                                                            (50, 50, 4),
                                                            (51, 51, 1),
                                                            (52, 52, 4),
                                                            (53, 53, 4),
                                                            (54, 54, 6),
                                                            (55, 55, 4),
                                                            (56, 56, 4),
                                                            (57, 57, 4),
                                                            (58, 58, 4),
                                                            (59, 59, 4),
                                                            (60, 60, 6),
                                                            (61, 61, 4),
                                                            (62, 62, 4),
                                                            (63, 63, 4),
                                                            (64, 64, 4),
                                                            (65, 65, 4),
                                                            (66, 66, 4),
                                                            (67, 67, 4),
                                                            (68, 68, 6),
                                                            (69, 69, 1),
                                                            (70, 70, 4),
                                                            (71, 71, 4),
                                                            (72, 72, 4),
                                                            (73, 73, 6),
                                                            (74, 74, 4),
                                                            (75, 75, 4),
                                                            (76, 76, 6),
                                                            (77, 77, 4),
                                                            (78, 78, 4),
                                                            (79, 79, 4),
                                                            (80, 80, 4),
                                                            (81, 81, 2),
                                                            (82, 82, 4),
                                                            (83, 83, 4),
                                                            (84, 84, 1),
                                                            (85, 85, 4),
                                                            (86, 86, 4),
                                                            (87, 87, 4),
                                                            (88, 88, 4),
                                                            (89, 89, 4),
                                                            (90, 90, 6),
                                                            (91, 91, 6),
                                                            (92, 92, 1),
                                                            (93, 93, 6),
                                                            (94, 94, 2),
                                                            (95, 95, 4);

    INSERT INTO book_variants (id, book_id, format, price, sale_price, stock) VALUES
                                                                                (1, 1, 'bìa mềm', 135400.00, 115000.00, 27),
                                                                                (2, 1, 'bìa cứng', 169000.00, 148000.00, 16),
                                                                                (3, 2, 'bìa mềm', 145900.00, 124000.00, 37),
                                                                                (4, 2, 'bìa cứng', 182000.00, 160000.00, 30),
                                                                                (5, 3, 'bìa mềm', 92800.00, 78000.00, 28),
                                                                                (6, 3, 'bìa cứng', 116000.00, 102000.00, 21),
                                                                                (7, 4, 'bìa mềm', 139200.00, 118000.00, 67),
                                                                                (8, 4, 'bìa cứng', 174000.00, 153000.00, 49),
                                                                                (9, 5, 'bìa mềm', 78900.00, 67000.00, 57),
                                                                                (10, 5, 'bìa cứng', 98000.00, 86000.00, 42),
                                                                                (11, 6, 'bìa mềm', 73200.00, 62000.00, 21),
                                                                                (12, 6, 'bìa cứng', 91000.00, 80000.00, 20),
                                                                                (13, 7, 'bìa mềm', 92300.00, 78000.00, 34),
                                                                                (14, 7, 'bìa cứng', 115000.00, 101000.00, 47),
                                                                                (15, 8, 'bìa mềm', 131600.00, 111000.00, 21),
                                                                                (16, 8, 'bìa cứng', 164000.00, 144000.00, 50),
                                                                                (17, 9, 'bìa mềm', 90300.00, 76000.00, 65),
                                                                                (18, 9, 'bìa cứng', 112000.00, 98000.00, 56),
                                                                                (19, 10, 'bìa mềm', 141800.00, 120000.00, 54),
                                                                                (20, 10, 'bìa cứng', 177000.00, 155000.00, 41),
                                                                                (21, 11, 'bìa mềm', 92500.00, 78000.00, 48),
                                                                                (22, 11, 'bìa cứng', 115000.00, 101000.00, 52),
                                                                                (23, 12, 'bìa mềm', 98400.00, 83000.00, 71),
                                                                                (24, 12, 'bìa cứng', 123000.00, 108000.00, 15),
                                                                                (25, 13, 'bìa mềm', 147700.00, 125000.00, 71),
                                                                                (26, 13, 'bìa cứng', 184000.00, 161000.00, 25),
                                                                                (27, 14, 'bìa mềm', 141400.00, 120000.00, 47),
                                                                                (28, 14, 'bìa cứng', 176000.00, 154000.00, 36),
                                                                                (29, 15, 'bìa mềm', 98400.00, 83000.00, 29),
                                                                                (30, 15, 'bìa cứng', 123000.00, 108000.00, 28),
                                                                                (31, 16, 'bìa mềm', 148100.00, 125000.00, 41),
                                                                                (32, 16, 'bìa cứng', 185000.00, 162000.00, 21),
                                                                                (33, 17, 'bìa mềm', 79400.00, 67000.00, 44),
                                                                                (34, 17, 'bìa cứng', 99000.00, 87000.00, 21),
                                                                                (35, 18, 'bìa mềm', 106700.00, 90000.00, 74),
                                                                                (36, 18, 'bìa cứng', 133000.00, 117000.00, 37),
                                                                                (37, 19, 'bìa mềm', 131800.00, 112000.00, 36),
                                                                                (38, 19, 'bìa cứng', 164000.00, 144000.00, 17),
                                                                                (39, 20, 'bìa mềm', 144700.00, 122000.00, 49),
                                                                                (40, 20, 'bìa cứng', 180000.00, 158000.00, 49),
                                                                                (41, 21, 'bìa mềm', 82700.00, 70000.00, 79),
                                                                                (42, 21, 'bìa cứng', 103000.00, 90000.00, 39),
                                                                                (43, 22, 'bìa mềm', 78000.00, 66000.00, 55),
                                                                                (44, 22, 'bìa cứng', 97000.00, 85000.00, 33),
                                                                                (45, 23, 'bìa mềm', 134300.00, 114000.00, 59),
                                                                                (46, 23, 'bìa cứng', 167000.00, 146000.00, 38),
                                                                                (47, 24, 'bìa mềm', 129100.00, 109000.00, 32),
                                                                                (48, 24, 'bìa cứng', 161000.00, 141000.00, 60),
                                                                                (49, 25, 'bìa mềm', 77100.00, 65000.00, 22),
                                                                                (50, 25, 'bìa cứng', 96000.00, 84000.00, 57),
                                                                                (51, 26, 'bìa mềm', 93300.00, 79000.00, 69),
                                                                                (52, 26, 'bìa cứng', 116000.00, 102000.00, 33),
                                                                                (53, 27, 'bìa mềm', 78100.00, 66000.00, 74),
                                                                                (54, 27, 'bìa cứng', 97000.00, 85000.00, 29),
                                                                                (55, 28, 'bìa mềm', 80300.00, 68000.00, 44),
                                                                                (56, 28, 'bìa cứng', 100000.00, 88000.00, 32),
                                                                                (57, 29, 'bìa mềm', 116400.00, 98000.00, 60),
                                                                                (58, 29, 'bìa cứng', 145000.00, 127000.00, 38),
                                                                                (59, 30, 'bìa mềm', 86600.00, 73000.00, 43),
                                                                                (60, 30, 'bìa cứng', 108000.00, 95000.00, 37),
                                                                                (61, 31, 'bìa mềm', 91400.00, 77000.00, 62),
                                                                                (62, 31, 'bìa cứng', 114000.00, 100000.00, 32),
                                                                                (63, 32, 'bìa mềm', 141800.00, 120000.00, 79),
                                                                                (64, 32, 'bìa cứng', 177000.00, 155000.00, 58),
                                                                                (65, 33, 'bìa mềm', 136300.00, 115000.00, 24),
                                                                                (66, 33, 'bìa cứng', 170000.00, 149000.00, 53),
                                                                                (67, 34, 'bìa mềm', 135000.00, 114000.00, 30),
                                                                                (68, 34, 'bìa cứng', 168000.00, 147000.00, 49),
                                                                                (69, 35, 'bìa mềm', 144600.00, 122000.00, 35),
                                                                                (70, 35, 'bìa cứng', 180000.00, 158000.00, 25),
                                                                                (71, 36, 'bìa mềm', 117300.00, 99000.00, 44),
                                                                                (72, 36, 'bìa cứng', 146000.00, 128000.00, 32),
                                                                                (73, 37, 'bìa mềm', 135500.00, 115000.00, 64),
                                                                                (74, 37, 'bìa cứng', 169000.00, 148000.00, 50),
                                                                                (75, 38, 'bìa mềm', 92400.00, 78000.00, 63),
                                                                                (76, 38, 'bìa cứng', 115000.00, 101000.00, 35),
                                                                                (77, 39, 'bìa mềm', 148600.00, 126000.00, 69),
                                                                                (78, 39, 'bìa cứng', 185000.00, 162000.00, 18),
                                                                                (79, 40, 'bìa mềm', 93400.00, 79000.00, 72),
                                                                                (80, 40, 'bìa cứng', 116000.00, 102000.00, 17),
                                                                                (81, 41, 'bìa mềm', 102300.00, 86000.00, 45),
                                                                                (82, 41, 'bìa cứng', 127000.00, 111000.00, 32),
                                                                                (83, 42, 'bìa mềm', 76700.00, 65000.00, 33),
                                                                                (84, 42, 'bìa cứng', 95000.00, 83000.00, 51),
                                                                                (85, 43, 'bìa mềm', 143500.00, 121000.00, 40),
                                                                                (86, 43, 'bìa cứng', 179000.00, 157000.00, 28),
                                                                                (87, 44, 'bìa mềm', 137100.00, 116000.00, 51),
                                                                                (88, 44, 'bìa cứng', 171000.00, 150000.00, 40),
                                                                                (89, 45, 'bìa mềm', 135800.00, 115000.00, 49),
                                                                                (90, 45, 'bìa cứng', 169000.00, 148000.00, 24),
                                                                                (91, 46, 'bìa mềm', 97100.00, 82000.00, 28),
                                                                                (92, 46, 'bìa cứng', 121000.00, 106000.00, 30),
                                                                                (93, 47, 'bìa mềm', 146200.00, 124000.00, 55),
                                                                                (94, 47, 'bìa cứng', 182000.00, 160000.00, 49),
                                                                                (95, 48, 'bìa mềm', 96900.00, 82000.00, 67),
                                                                                (96, 48, 'bìa cứng', 121000.00, 106000.00, 52),
                                                                                (97, 49, 'bìa mềm', 113800.00, 96000.00, 77),
                                                                                (98, 49, 'bìa cứng', 142000.00, 124000.00, 52),
                                                                                (99, 50, 'bìa mềm', 110800.00, 94000.00, 43),
                                                                                (100, 50, 'bìa cứng', 138000.00, 121000.00, 29),
                                                                                (101, 51, 'bìa mềm', 84100.00, 71000.00, 52),
                                                                                (102, 51, 'bìa cứng', 105000.00, 92000.00, 46),
                                                                                (103, 52, 'bìa mềm', 79300.00, 67000.00, 68),
                                                                                (104, 52, 'bìa cứng', 99000.00, 87000.00, 18),
                                                                                (105, 53, 'bìa mềm', 81200.00, 69000.00, 29),
                                                                                (106, 53, 'bìa cứng', 101000.00, 88000.00, 55),
                                                                                (107, 54, 'bìa mềm', 86300.00, 73000.00, 70),
                                                                                (108, 54, 'bìa cứng', 107000.00, 94000.00, 58),
                                                                                (109, 55, 'bìa mềm', 113200.00, 96000.00, 58),
                                                                                (110, 55, 'bìa cứng', 141000.00, 124000.00, 19),
                                                                                (111, 56, 'bìa mềm', 109400.00, 92000.00, 44),
                                                                                (112, 56, 'bìa cứng', 136000.00, 119000.00, 53),
                                                                                (113, 57, 'bìa mềm', 117900.00, 100000.00, 53),
                                                                                (114, 57, 'bìa cứng', 147000.00, 129000.00, 31),
                                                                                (115, 58, 'bìa mềm', 126600.00, 107000.00, 75),
                                                                                (116, 58, 'bìa cứng', 158000.00, 139000.00, 15),
                                                                                (117, 59, 'bìa mềm', 139600.00, 118000.00, 66),
                                                                                (118, 59, 'bìa cứng', 174000.00, 153000.00, 22),
                                                                                (119, 60, 'bìa mềm', 139800.00, 118000.00, 76),
                                                                                (120, 60, 'bìa cứng', 174000.00, 153000.00, 49),
                                                                                (121, 61, 'bìa mềm', 146800.00, 124000.00, 37),
                                                                                (122, 61, 'bìa cứng', 183000.00, 161000.00, 56),
                                                                                (123, 62, 'bìa mềm', 104800.00, 89000.00, 27),
                                                                                (124, 62, 'bìa cứng', 131000.00, 115000.00, 33),
                                                                                (125, 63, 'bìa mềm', 114500.00, 97000.00, 30),
                                                                                (126, 63, 'bìa cứng', 143000.00, 125000.00, 44),
                                                                                (127, 64, 'bìa mềm', 70300.00, 59000.00, 66),
                                                                                (128, 64, 'bìa cứng', 87000.00, 76000.00, 31),
                                                                                (129, 65, 'bìa mềm', 121200.00, 103000.00, 68),
                                                                                (130, 65, 'bìa cứng', 151000.00, 132000.00, 26),
                                                                                (131, 66, 'bìa mềm', 121900.00, 103000.00, 78),
                                                                                (132, 66, 'bìa cứng', 152000.00, 133000.00, 21),
                                                                                (133, 67, 'bìa mềm', 134000.00, 113000.00, 39),
                                                                                (134, 67, 'bìa cứng', 167000.00, 146000.00, 55),
                                                                                (135, 68, 'bìa mềm', 121900.00, 103000.00, 58),
                                                                                (136, 68, 'bìa cứng', 152000.00, 133000.00, 27),
                                                                                (137, 69, 'bìa mềm', 85600.00, 72000.00, 43),
                                                                                (138, 69, 'bìa cứng', 107000.00, 94000.00, 25),
                                                                                (139, 70, 'bìa mềm', 125200.00, 106000.00, 69),
                                                                                (140, 70, 'bìa cứng', 156000.00, 137000.00, 48),
                                                                                (141, 71, 'bìa mềm', 70000.00, 59000.00, 58),
                                                                                (142, 71, 'bìa cứng', 87000.00, 76000.00, 35),
                                                                                (143, 72, 'bìa mềm', 120000.00, 102000.00, 21),
                                                                                (144, 72, 'bìa cứng', 150000.00, 132000.00, 22),
                                                                                (145, 73, 'bìa mềm', 107100.00, 91000.00, 76),
                                                                                (146, 73, 'bìa cứng', 133000.00, 117000.00, 34),
                                                                                (147, 74, 'bìa mềm', 94500.00, 80000.00, 23),
                                                                                (148, 74, 'bìa cứng', 118000.00, 103000.00, 30),
                                                                                (149, 75, 'bìa mềm', 128000.00, 108000.00, 80),
                                                                                (150, 75, 'bìa cứng', 160000.00, 140000.00, 20),
                                                                                (151, 76, 'bìa mềm', 78700.00, 66000.00, 66),
                                                                                (152, 76, 'bìa cứng', 98000.00, 86000.00, 46),
                                                                                (153, 77, 'bìa mềm', 77000.00, 65000.00, 68),
                                                                                (154, 77, 'bìa cứng', 96000.00, 84000.00, 49),
                                                                                (155, 78, 'bìa mềm', 148400.00, 126000.00, 28),
                                                                                (156, 78, 'bìa cứng', 185000.00, 162000.00, 23),
                                                                                (157, 79, 'bìa mềm', 137500.00, 116000.00, 50),
                                                                                (158, 79, 'bìa cứng', 171000.00, 150000.00, 50),
                                                                                (159, 80, 'bìa mềm', 86900.00, 73000.00, 36),
                                                                                (160, 80, 'bìa cứng', 108000.00, 95000.00, 48),
                                                                                (161, 81, 'bìa mềm', 132100.00, 112000.00, 47),
                                                                                (162, 81, 'bìa cứng', 165000.00, 145000.00, 28),
                                                                                (163, 82, 'bìa mềm', 125200.00, 106000.00, 68),
                                                                                (164, 82, 'bìa cứng', 156000.00, 137000.00, 59),
                                                                                (165, 83, 'bìa mềm', 90500.00, 76000.00, 65),
                                                                                (166, 83, 'bìa cứng', 113000.00, 99000.00, 34),
                                                                                (167, 84, 'bìa mềm', 110800.00, 94000.00, 62),
                                                                                (168, 84, 'bìa cứng', 138000.00, 121000.00, 56),
                                                                                (169, 85, 'bìa mềm', 108200.00, 91000.00, 48),
                                                                                (170, 85, 'bìa cứng', 135000.00, 118000.00, 48),
                                                                                (171, 86, 'bìa mềm', 116200.00, 98000.00, 27),
                                                                                (172, 86, 'bìa cứng', 145000.00, 127000.00, 30),
                                                                                (173, 87, 'bìa mềm', 93000.00, 79000.00, 24),
                                                                                (174, 87, 'bìa cứng', 116000.00, 102000.00, 36),
                                                                                (175, 88, 'bìa mềm', 72100.00, 61000.00, 57),
                                                                                (176, 88, 'bìa cứng', 90000.00, 79000.00, 50),
                                                                                (177, 89, 'bìa mềm', 93500.00, 79000.00, 57),
                                                                                (178, 89, 'bìa cứng', 116000.00, 102000.00, 29),
                                                                                (179, 90, 'bìa mềm', 70700.00, 60000.00, 24),
                                                                                (180, 90, 'bìa cứng', 88000.00, 77000.00, 60),
                                                                                (181, 91, 'bìa mềm', 134600.00, 114000.00, 23),
                                                                                (182, 91, 'bìa cứng', 168000.00, 147000.00, 29),
                                                                                (183, 92, 'bìa mềm', 76900.00, 65000.00, 77),
                                                                                (184, 92, 'bìa cứng', 96000.00, 84000.00, 17),
                                                                                (185, 93, 'bìa mềm', 103800.00, 88000.00, 24),
                                                                                (186, 93, 'bìa cứng', 129000.00, 113000.00, 47),
                                                                                (187, 94, 'bìa mềm', 94300.00, 80000.00, 37),
                                                                                (188, 94, 'bìa cứng', 117000.00, 102000.00, 57),
                                                                                (189, 95, 'bìa mềm', 119700.00, 101000.00, 33),
                                                                                (190, 95, 'bìa cứng', 149000.00, 131000.00, 49);

    INSERT INTO book_images (id, book_id, image_url, sort_order) VALUES
                                                                    (1, 1, 'images/books/1984.webp', 0),
                                                                    (2, 2, 'images/books/7-thoi-quen.webp', 0),
                                                                    (3, 3, 'images/books/anne-toc-do-duoi-chai-nha-xanh.webp', 0),
                                                                    (4, 4, 'images/books/atomic-habits.webp', 0),
                                                                    (5, 5, 'images/books/ban-do-tu-duy.webp', 0),
                                                                    (6, 6, 'images/books/bi-mat-tu-duy-trieu-phu.webp', 0),
                                                                    (7, 7, 'images/books/bien-nien-ky-chim-van-day-cot.webp', 0),
                                                                    (8, 8, 'images/books/bo-gia.webp', 0),
                                                                    (9, 9, 'images/books/cha-giau-cha-ngheo.webp', 0),
                                                                    (10, 10, 'images/books/chi-pheo.webp', 0),
                                                                    (11, 11, 'images/books/chien-luoc-dai-duong-xanh.webp', 0),
                                                                    (12, 12, 'images/books/cho-toi-xin-mot-ve-di-tuoi-tho.webp', 0),
                                                                    (13, 13, 'images/books/chu-be-mang-pyjama-soc.webp', 0),
                                                                    (14, 14, 'images/books/clean-code.webp', 0),
                                                                    (15, 15, 'images/books/co-the-tu-chua-lanh.webp', 0),
                                                                    (16, 16, 'images/books/cuon-theo-chieu-gio.webp', 0),
                                                                    (17, 17, 'images/books/dac-nhan-tam.webp', 0),
                                                                    (18, 18, 'images/books/dai-viet-su-ky-toan-thu.webp', 0),
                                                                    (19, 19, 'images/books/dam-bi-ghet.webp', 0),
                                                                    (20, 20, 'images/books/de-men-phieu-luu-ky.webp', 0),
                                                                    (21, 21, 'images/books/deep-work.webp', 0),
                                                                    (22, 22, 'images/books/di-tim-le-song.webp', 0),
                                                                    (23, 23, 'images/books/dieu-ky-dieu-cua-tiem-thuc.webp', 0),
                                                                    (24, 24, 'images/books/digital-minimalism.webp', 0),
                                                                    (25, 25, 'images/books/doi-gio-hu.webp', 0),
                                                                    (26, 26, 'images/books/educated.webp', 0),
                                                                    (27, 27, 'images/books/eloquent-javascript.webp', 0),
                                                                    (28, 28, 'images/books/essentialism.webp', 0),
                                                                    (29, 29, 'images/books/factfulness.webp', 0),
                                                                    (30, 30, 'images/books/flow-dong-chay.webp', 0),
                                                                    (31, 31, 'images/books/freakonomics.webp', 0),
                                                                    (32, 32, 'images/books/gene-lich-su-va-tuong-lai.webp', 0),
                                                                    (33, 33, 'images/books/giet-con-chim-nhai.webp', 0),
                                                                    (34, 34, 'images/books/hai-so-phan.webp', 0),
                                                                    (35, 35, 'images/books/harry-potter-va-hon-da-phu-thuy.webp', 0),
                                                                    (36, 36, 'images/books/harry-potter-va-phong-chua-bi-mat.webp', 0),
                                                                    (37, 37, 'images/books/hoang-tu-be.webp', 0),
                                                                    (38, 38, 'images/books/hoang-tu-va-nguoi-an-may.webp', 0),
                                                                    (39, 39, 'images/books/hoc-cach-hoc.webp', 0),
                                                                    (40, 40, 'images/books/homo-deus-luoc-su-tuong-lai.webp', 0),
                                                                    (41, 41, 'images/books/hooked.webp', 0),
                                                                    (42, 42, 'images/books/ikigai.webp', 0),
                                                                    (43, 43, 'images/books/kafka-ben-bo-bien.webp', 0),
                                                                    (44, 44, 'images/books/khong-gia-dinh.webp', 0),
                                                                    (45, 45, 'images/books/kinh-te-hoc-hai-huoc.webp', 0),
                                                                    (46, 46, 'images/books/lich-su-my.webp', 0),
                                                                    (47, 47, 'images/books/loi-thu-toi.webp', 0),
                                                                    (48, 48, 'images/books/luoc-su-thoi-gian.webp', 0),
                                                                    (49, 49, 'images/books/make-time.webp', 0),
                                                                    (50, 50, 'images/books/mans-search-for-meanin.webp', 0),
                                                                    (51, 51, 'images/books/mat-biec.webp', 0),
                                                                    (52, 52, 'images/books/nghe-thuat-noi-truoc-cong-chung.webp', 0),
                                                                    (53, 53, 'images/books/nghe-thuat-song-toi-gian.webp', 0),
                                                                    (54, 54, 'images/books/nguon-goc-cac-loai.webp', 0),
                                                                    (55, 55, 'images/books/nha-gia-kim.webp', 0),
                                                                    (56, 56, 'images/books/nha-lanh-dao-khong-chuc-danh.webp', 0),
                                                                    (57, 57, 'images/books/nhung-cuoc-thap-tu-chinh.webp', 0),
                                                                    (58, 58, 'images/books/nhung-nguoi-khon-kho.webp', 0),
                                                                    (59, 59, 'images/books/nhung-quy-luat-cua-ban-chat-con-nguoi.webp', 0),
                                                                    (60, 60, 'images/books/nhung-tu-nhan-dia-ly.webp', 0),
                                                                    (61, 61, 'images/books/norwegian-wood-ban-tieng-anh.webp', 0),
                                                                    (62, 62, 'images/books/ong-gia-va-bien-ca.webp', 0),
                                                                    (63, 63, 'images/books/ong-tram-tuoi-treo-qua-cua-so-va-bien-mat.webp', 0),
                                                                    (64, 64, 'images/books/outliers.webp', 0),
                                                                    (65, 65, 'images/books/quiet.webp', 0),
                                                                    (66, 66, 'images/books/rework.webp', 0),
                                                                    (67, 67, 'images/books/rung-na-uy.webp', 0),
                                                                    (68, 68, 'images/books/sapiens-luoc-su-loai-nguoi.webp', 0),
                                                                    (69, 69, 'images/books/so-do.webp', 0),
                                                                    (70, 70, 'images/books/song-doi-tu-do.webp', 0),
                                                                    (71, 71, 'images/books/start-with-why.webp', 0),
                                                                    (72, 72, 'images/books/suc-manh-cua-su-tap-trung.webp', 0),
                                                                    (73, 73, 'images/books/sung-vi-trung-va-thep.webp', 0),
                                                                    (74, 74, 'images/books/tam-ly-hoc-dam-dong.webp', 0),
                                                                    (75, 75, 'images/books/the-lean-startup.webp', 0),
                                                                    (76, 76, 'images/books/the-pragmatic-programmer.webp', 0),
                                                                    (77, 77, 'images/books/thien-tai-ben-trong.webp', 0),
                                                                    (78, 78, 'images/books/thien-va-nghe-thuat-bao-duong-xe-may.webp', 0),
                                                                    (79, 79, 'images/books/thinking-fast-and-slow-english.webp', 0),
                                                                    (80, 80, 'images/books/toi-ac-va-trung-phat.webp', 0),
                                                                    (81, 81, 'images/books/totto-chan-ben-cua-so.webp', 0),
                                                                    (82, 82, 'images/books/tram-nam-co-don.webp', 0),
                                                                    (83, 83, 'images/books/tri-tue-cam-xuc.webp', 0),
                                                                    (84, 84, 'images/books/truyen-kieu.webp', 0),
                                                                    (85, 85, 'images/books/tu-duy-lam-giau.webp', 0),
                                                                    (86, 86, 'images/books/tu-duy-nhanh-va-cham.webp', 0),
                                                                    (87, 87, 'images/books/tu-duy-phan-bien.webp', 0),
                                                                    (88, 88, 'images/books/tu-tot-den-vi-dai.webp', 0),
                                                                    (89, 89, 'images/books/tuan-lam-viec-4-gio.webp', 0),
                                                                    (90, 90, 'images/books/vat-ly-cua-tuong-lai.webp', 0),
                                                                    (91, 91, 'images/books/viet-nam-su-luoc.webp', 0),
                                                                    (92, 92, 'images/books/vo-chong-a-phu.webp', 0),
                                                                    (93, 93, 'images/books/vu-tru-trong-vo-hat-de.webp', 0),
                                                                    (94, 94, 'images/books/vua-nham-mat-vua-mo-cua-so.webp', 0),
                                                                    (95, 95, 'images/books/zero-to-one.webp', 0);

    -- ============================================
    -- 1) USERS (5 user)
    -- Password = 123456 (bcrypt)
    -- ============================================
    INSERT INTO users (id, name, email, password, role, is_active) VALUES
                                                                    (1, 'Admin System', 'admin@wise.local',
                                                                        '$2y$10$H9EJpWM18r4.KpV7H5ZoeONIAVhYoifdaP.4KvaRxXuJJm9t46xLe', 'admin', 1),

                                                                    (2, 'Nguyễn Văn A', 'a@wise.local',
                                                                        '$2y$10$pjC161ShZr1Fkj711onlB.zP45o22OFL2/EpgUdnYyqV6zFHPcAHW', 'user', 1),

                                                                    (3, 'Trần Thị B', 'b@wise.local',
                                                                        '$2y$10$pjC161ShZr1Fkj711onlB.zP45o22OFL2/EpgUdnYyqV6zFHPcAHW', 'user', 1),

                                                                    (4, 'Lê Văn C', 'c@wise.local',
                                                                        '$2y$10$pjC161ShZr1Fkj711onlB.zP45o22OFL2/EpgUdnYyqV6zFHPcAHW', 'user', 1),

                                                                    (5, 'Phạm Thị D', 'd@wise.local',
                                                                        '$2y$10$pjC161ShZr1Fkj711onlB.zP45o22OFL2/EpgUdnYyqV6zFHPcAHW', 'user', 1);

    -- ============================================
    -- 2) USER ADDRESS (mỗi user 2 địa chỉ)
    -- ============================================
    INSERT INTO user_address (id, user_id, full_address, shipping_phone, is_default) VALUES
                                                                                        (1, 2, '12 Nguyễn Trãi, Q1, TP.HCM', '0909000001', 1),
                                                                                        (2, 2, '45 Lê Lợi, Q1, TP.HCM', '0909000002', 0),

                                                                                        (3, 3, '88 Điện Biên Phủ, Bình Thạnh, TP.HCM', '0909000003', 1),
                                                                                        (4, 3, '102 Phan Xích Long, Phú Nhuận, TP.HCM', '0909000004', 0),

                                                                                        (5, 4, '15 Trần Hưng Đạo, Đà Nẵng', '0909000005', 1),
                                                                                        (6, 4, '200 Nguyễn Văn Linh, Đà Nẵng', '0909000006', 0),

                                                                                        (7, 5, '33 Võ Thị Sáu, Hà Nội', '0909000007', 1),
                                                                                        (8, 5, '77 Xuân Thủy, Cầu Giấy, Hà Nội', '0909000008', 0);

    -- ============================================
    -- 3) WISHLIST (20 item)
    -- dùng book_id 1..100
    -- ============================================
    INSERT INTO wishlist (id, book_id, user_id) VALUES
                                                    (1,  3, 2), (2,  5, 2), (3, 11, 2), (4, 15, 2), (5, 22, 2),
                                                    (6,  7, 3), (7,  9, 3), (8, 25, 3), (9, 31, 3), (10, 44, 3),
                                                    (11, 2, 4), (12, 6, 4), (13, 10, 4), (14, 28, 4), (15, 39, 4),
                                                    (16, 1, 5), (17, 4, 5), (18, 12, 5), (19, 19, 5), (20, 55, 5);

    -- ============================================
    -- 4) COMMENTS (20 review)
    -- ============================================
    INSERT INTO comments (id, book_id, user_id, content, rating) VALUES
                                                                    (1, 1, 2, 'Sách rất hay và dễ hiểu.', 5),
                                                                    (2, 1, 3, 'Khá thú vị, đáng đọc.', 4),
                                                                    (3, 3, 2, 'Nội dung sâu sắc.', 5),
                                                                    (4, 3, 4, 'Hơi khó đọc nhưng giá trị.', 4),
                                                                    (5, 5, 3, 'Một trong những cuốn hay nhất.', 5),
                                                                    (6, 7, 5, 'Cảm xúc và ý nghĩa.', 5),
                                                                    (7, 8, 2, 'Tác phẩm kinh điển, rất ấn tượng.', 5),
                                                                    (8, 9, 3, 'Murakami chưa bao giờ làm tôi thất vọng.', 5),
                                                                    (9, 10, 4, 'Đọc xong suy nghĩ nhiều.', 4),
                                                                    (10, 11, 2, 'Cảm động và ám ảnh.', 5),
                                                                    (11, 12, 3, 'Đỉnh cao văn học Nga.', 5),
                                                                    (12, 15, 5, 'Thay đổi tư duy của tôi.', 5),
                                                                    (13, 16, 4, 'Hữu ích cho người trẻ.', 4),
                                                                    (14, 17, 5, 'Xuất sắc!', 5),
                                                                    (15, 19, 2, 'Kinh điển self-help.', 5),
                                                                    (16, 22, 3, 'Deep và đáng suy ngẫm.', 4),
                                                                    (17, 25, 4, 'Nguyễn Nhật Ánh quá tuyệt.', 5),
                                                                    (18, 28, 5, 'Tuổi thơ quay về.', 5),
                                                                    (19, 31, 3, 'Văn phong đẹp.', 4),
                                                                    (20, 44, 2, 'Nội dung rất mới mẻ.', 4);

    -- ============================================
    -- 5) ORDERS (5 order)
    -- snapshot địa chỉ
    -- ============================================
    INSERT INTO orders (id, user_id, user_address_id, total, shipping_status,
                        shipping_address, shipping_phone, note)
    VALUES
        (1, 2, 1, 320000, 'delivered', '12 Nguyễn Trãi, Q1, TP.HCM', '0909000001', 'Giao giờ hành chính'),
        (2, 3, 3, 455000, 'shipped',   '88 Điện Biên Phủ, Bình Thạnh, TP.HCM', '0909000003', NULL),
        (3, 2, 2, 298000, 'processing','45 Lê Lợi, Q1, TP.HCM', '0909000002', NULL),
        (4, 4, 5, 188000, 'delivered', '15 Trần Hưng Đạo, Đà Nẵng', '0909000005', NULL),
        (5, 5, 7, 283000, 'shipped',   '33 Võ Thị Sáu, Hà Nội', '0909000007', 'Giao nhanh giúp tôi');

    -- ============================================
    -- 6) ORDER ITEMS (map đúng variant_id mới)
    -- variant_id = (book_id - 1)*2 + 1  (bìa mềm)
    -- variant_id = (book_id - 1)*2 + 2  (bìa cứng)
    -- ============================================
    INSERT INTO order_items (id, order_id, variant_id, quantity, price, subtotal) VALUES
    -- Order 1
    (1, 1,  (1-1)*2+1, 1, 65000, 65000),
    (2, 1,  (7-1)*2+1, 1, 99000, 99000),
    (3, 1,  (15-1)*2+2, 1, 156000, 156000),

    -- Order 2
    (4, 2,  (3-1)*2+1, 1, 86000, 86000),
    (5, 2,  (10-1)*2+2, 1, 136000, 136000),
    (6, 2,  (11-1)*2+1, 1, 89000, 89000),

    -- Order 3
    (7, 3,  (2-1)*2+1, 1, 72000, 72000),
    (8, 3,  (9-1)*2+2, 1, 144000, 144000),

    -- Order 4
    (9, 4,  (4-1)*2+1, 1, 64000, 64000),
    (10,4,  (8-1)*2+2, 1, 54000, 54000),

    -- Order 5
    (11,5, (17-1)*2+1, 1, 88000, 88000),
    (12,5, (22-1)*2+1, 1, 90000, 90000),
    (13,5, (29-1)*2+2, 1, 105000, 105000);

    -- ============================================
    -- 7) PAYMENT
    -- ============================================
    INSERT INTO payment (order_id, payment_method) VALUES
                                                    (1, 'cod'),
                                                    (2, 'card'),
                                                    (3, 'cod'),
                                                    (4, 'card'),
                                                    (5, 'cod');



    SET FOREIGN_KEY_CHECKS = 0;
    UPDATE books SET category_id = 7 WHERE id = 1;
    UPDATE books SET category_id = 14 WHERE id = 2;
    UPDATE books SET category_id = 17 WHERE id = 3;
    UPDATE books SET category_id = 14 WHERE id = 4;
    UPDATE books SET category_id = 14 WHERE id = 5;
    UPDATE books SET category_id = 12 WHERE id = 6;
    UPDATE books SET category_id = 7 WHERE id = 7;
    UPDATE books SET category_id = 7 WHERE id = 8;
    UPDATE books SET category_id = 12 WHERE id = 9;
    UPDATE books SET category_id = 6 WHERE id = 10;
    UPDATE books SET category_id = 11 WHERE id = 11;
    UPDATE books SET category_id = 17 WHERE id = 12;
    UPDATE books SET category_id = 17 WHERE id = 13;
    UPDATE books SET category_id = 15 WHERE id = 14;
    UPDATE books SET category_id = 13 WHERE id = 15;
    UPDATE books SET category_id = 7 WHERE id = 16;
    UPDATE books SET category_id = 13 WHERE id = 17;
    UPDATE books SET category_id = 6 WHERE id = 18;
    UPDATE books SET category_id = 13 WHERE id = 19;
    UPDATE books SET category_id = 16 WHERE id = 20;
    UPDATE books SET category_id = 15 WHERE id = 21;
    UPDATE books SET category_id = 13 WHERE id = 22;
    UPDATE books SET category_id = 13 WHERE id = 23;
    UPDATE books SET category_id = 15 WHERE id = 24;
    UPDATE books SET category_id = 6 WHERE id = 25;
    UPDATE books SET category_id = 13 WHERE id = 26;
    UPDATE books SET category_id = 15 WHERE id = 27;
    UPDATE books SET category_id = 14 WHERE id = 28;
    UPDATE books SET category_id = 10 WHERE id = 29;
    UPDATE books SET category_id = 14 WHERE id = 30;
    UPDATE books SET category_id = 10 WHERE id = 31;
    UPDATE books SET category_id = 9 WHERE id = 32;
    UPDATE books SET category_id = 7 WHERE id = 33;
    UPDATE books SET category_id = 7 WHERE id = 34;
    UPDATE books SET category_id = 17 WHERE id = 35;
    UPDATE books SET category_id = 17 WHERE id = 36;
    UPDATE books SET category_id = 16 WHERE id = 37;
    UPDATE books SET category_id = 16 WHERE id = 38;
    UPDATE books SET category_id = 14 WHERE id = 39;
    UPDATE books SET category_id = 9 WHERE id = 40;
    UPDATE books SET category_id = 11 WHERE id = 41;
    UPDATE books SET category_id = 13 WHERE id = 42;
    UPDATE books SET category_id = 7 WHERE id = 43;
    UPDATE books SET category_id = 16 WHERE id = 44;
    UPDATE books SET category_id = 10 WHERE id = 45;
    UPDATE books SET category_id = 10 WHERE id = 46;
    UPDATE books SET category_id = 7 WHERE id = 47;
    UPDATE books SET category_id = 8 WHERE id = 48;
    UPDATE books SET category_id = 15 WHERE id = 49;
    UPDATE books SET category_id = 13 WHERE id = 50;
    UPDATE books SET category_id = 6 WHERE id = 51;
    UPDATE books SET category_id = 13 WHERE id = 52;
    UPDATE books SET category_id = 13 WHERE id = 53;
    UPDATE books SET category_id = 9 WHERE id = 54;
    UPDATE books SET category_id = 7 WHERE id = 55;
    UPDATE books SET category_id = 11 WHERE id = 56;
    UPDATE books SET category_id = 10 WHERE id = 57;
    UPDATE books SET category_id = 7 WHERE id = 58;
    UPDATE books SET category_id = 14 WHERE id = 59;
    UPDATE books SET category_id = 10 WHERE id = 60;
    UPDATE books SET category_id = 7 WHERE id = 61;
    UPDATE books SET category_id = 7 WHERE id = 62;
    UPDATE books SET category_id = 7 WHERE id = 63;
    UPDATE books SET category_id = 10 WHERE id = 64;
    UPDATE books SET category_id = 13 WHERE id = 65;
    UPDATE books SET category_id = 11 WHERE id = 66;
    UPDATE books SET category_id = 7 WHERE id = 67;
    UPDATE books SET category_id = 9 WHERE id = 68;
    UPDATE books SET category_id = 6 WHERE id = 69;
    UPDATE books SET category_id = 13 WHERE id = 70;
    UPDATE books SET category_id = 11 WHERE id = 71;
    UPDATE books SET category_id = 15 WHERE id = 72;
    UPDATE books SET category_id = 9 WHERE id = 73;
    UPDATE books SET category_id = 10 WHERE id = 74;
    UPDATE books SET category_id = 11 WHERE id = 75;
    UPDATE books SET category_id = 15 WHERE id = 76;
    UPDATE books SET category_id = 15 WHERE id = 77;
    UPDATE books SET category_id = 7 WHERE id = 78;
    UPDATE books SET category_id = 14 WHERE id = 79;
    UPDATE books SET category_id = 7 WHERE id = 80;
    UPDATE books SET category_id = 16 WHERE id = 81;
    UPDATE books SET category_id = 7 WHERE id = 82;
    UPDATE books SET category_id = 14 WHERE id = 83;
    UPDATE books SET category_id = 6 WHERE id = 84;
    UPDATE books SET category_id = 12 WHERE id = 85;
    UPDATE books SET category_id = 14 WHERE id = 86;
    UPDATE books SET category_id = 14 WHERE id = 87;
    UPDATE books SET category_id = 11 WHERE id = 88;
    UPDATE books SET category_id = 12 WHERE id = 89;
    UPDATE books SET category_id = 8 WHERE id = 90;
    UPDATE books SET category_id = 6 WHERE id = 91;
    UPDATE books SET category_id = 6 WHERE id = 92;
    UPDATE books SET category_id = 8 WHERE id = 93;
    UPDATE books SET category_id = 17 WHERE id = 94;
    UPDATE books SET category_id = 11 WHERE id = 95;
    SET FOREIGN_KEY_CHECKS = 1;

    INSERT INTO users (id, name, email, password, role, is_active) VALUES
    (13, 'gb1309', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);