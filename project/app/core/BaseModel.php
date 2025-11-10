<?php

/**
 * LỚP MODEL CHA (BaseModel)
 *
 * Mục đích: Đây là lớp "Core" mà TẤT CẢ các Model khác
 * (như Category, Book, User) sẽ kế thừa.
 *
 * Nó cung cấp sẵn các hàm CRUD (Thêm, Sửa, Xóa, Tìm kiếm)
 * dùng chung, giúp chúng ta không phải viết lại code.
 *
 * CÁCH DÙNG:
 * 1. Tạo file model con, ví dụ: `Category.php`
 * 2. Khai báo: `class Category extends BaseModel { ... }`
 * 3. Bên trong class con, BẮT BUỘC khai báo 2 biến:
 * protected $table = 'ten_bang'; (vd: 'categories')
 * protected $primaryKey = 'ten_khoa_chinh'; (vd: 'id')
 */
abstract class BaseModel {
    protected $db; // Biến chứa kết nối PDO

    /**
     * @var string Tên bảng trong CSDL (BẮT BUỘC phải khai báo ở model con)
     * Ví dụ: protected $table = 'books';
     */
    protected $table;

    /**
     * @var string Tên khóa chính của bảng (BẮT BUỘC phải khai báo ở model con)
     * Ví dụ: protected $primaryKey = 'id';
     */
    protected $primaryKey;

    public function __construct() {
        // Tự động lấy kết nối PDO khi 1 model con được gọi
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Tìm kiếm nhiều bản ghi (record)
     *
     * @param array $conditions Mảng điều kiện WHERE. Ví dụ: ['category_id' => 5, 'is_active' => 1]
     * @param int|null $limit Giới hạn số lượng kết quả trả về.
     * @param int|null $offset Số lượng bản ghi cần bỏ qua (dùng cho phân trang).
     * @return array Trả về một mảng chứa các bản ghi (dưới dạng mảng associative).
     *
     * @example $bookModel->find(['category_id' => 1], 10, 0); // Tìm 10 sách
     */
    public function find($conditions = [], $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        // Xử lý điều kiện WHERE (nếu có)
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $val) {
                // Thêm điều kiện an toàn (vd: "category_id = :category_id")
                $where[] = "$key = :$key";
                $params[":$key"] = $val;
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // Xử lý LIMIT và OFFSET (Phải ép kiểu INT để chống SQL Injection)
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        if ($offset !== null) {
            $sql .= " OFFSET " . (int)$offset;
        }

        // Chuẩn bị và thực thi câu lệnh
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm CHỈ MỘT bản ghi.
     *
     * @param array $conditions Mảng điều kiện WHERE. Ví dụ: ['id' => 1] hoặc ['email' => 'a@b.com']
     * @return array|null Trả về 1 mảng (bản ghi) nếu tìm thấy, ngược lại trả về null.
     *
     * @example $userModel->findOne(['id' => 10]);
     */
    public function findOne($conditions) {
        // Tận dụng hàm find() ở trên và giới hạn 1 kết quả
        $result = $this->find($conditions, 1);
        // Nếu $result có dữ liệu (không rỗng), trả về phần tử đầu tiên (index 0)
        return $result ? $result[0] : null;
    }

    /**
     * Thêm mới một bản ghi vào CSDL.
     *
     * @param array $data Mảng dữ liệu cần thêm. Ví dụ: ['name' => 'Sách ABC', 'price' => 100]
     * @return bool Trả về true nếu thêm thành công, false nếu thất bại.
     *
     * @example $bookModel->insert(['title' => 'Sách mới', 'category_id' => 1]);
     */
    public function insert($data) {
        $keys = array_keys($data); // Lấy ra danh sách các cột (vd: 'title', 'category_id')
        $fields = implode(', ', $keys); // Nối lại thành "title, category_id"
        $placeholders = ':' . implode(', :', $keys); // Nối lại thành ":title, :category_id"

        // Câu SQL cuối cùng: INSERT INTO ten_bang (title, category_id) VALUES (:title, :category_id)
        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        
        $stmt = $this->db->prepare($sql);
        
        // $data (vd: ['title' => 'Sách mới']) sẽ tự động map vào (:title) khi execute
        return $stmt->execute($data);
    }

    /**
     * Cập nhật một bản ghi dựa trên ID (khóa chính).
     *
     * @param int|string $id Giá trị của khóa chính (vd: 5).
     * @param array $data Mảng dữ liệu cần cập nhật. Ví dụ: ['name' => 'Sách đã sửa', 'price' => 150]
     * @return bool Trả về true nếu cập nhật thành công, false nếu thất bại.
     *
     * @example $bookModel->update(10, ['title' => 'Tiêu đề mới']);
     */
    public function update($id, $data) {
        $set = [];
        // Tạo ra chuỗi "key1 = :key1, key2 = :key2"
        foreach ($data as $key => $val) {
            $set[] = "$key = :$key";
        }

        // Câu SQL: UPDATE ten_bang SET title = :title, price = :price WHERE id = :id
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = :id";
        
        // Gán giá trị $id vào key ':id' để bind vào câu SQL
        $data['id'] = $id; 

        $stmt = $this->db->prepare($sql);
        
        // $data (vd: ['title' => 'Sách mới', 'id' => 10]) sẽ map vào :title và :id
        return $stmt->execute($data);
    }

    /**
     * Xóa một bản ghi dựa trên ID (khóa chính).
     *
     * @param int|string $id Giá trị của khóa chính cần xóa.
     * @return bool Trả về true nếu xóa thành công, false nếu thất bại.
     *
     * @example $bookModel->delete(10);
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]); // Thực thi với mảng chứa ID
    }

    /**
     * Đếm tổng số bản ghi (thường dùng cho phân trang).
     *
     * @param array $conditions Mảng điều kiện WHERE (nếu muốn đếm có điều kiện).
     * @return int Số lượng bản ghi đếm được.
     *
     * @example $totalBooks = $bookModel->count(['category_id' => 1]);
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];

        // Xử lý điều kiện WHERE (tương tự hàm find)
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $val) {
                $where[] = "$key = :$key";
                $params[":$key"] = $val;
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn(); // Trả về giá trị của cột đầu tiên (COUNT(*))
    }

    /**
     * Lấy dữ liệu đã được phân trang.
     *
     * @param int $limit Số lượng bản ghi trên 1 trang.
     * @param int $page Trang hiện tại (bắt đầu từ 1).
     * @param array $conditions Mảng điều kiện WHERE (nếu có).
     * @return array Một mảng chứa dữ liệu và thông tin phân trang.
     *
     * @example $paginationData = $bookModel->paginate(10, 2); // Lấy 10 sách ở trang số 2
     */
    public function paginate($limit, $page, $conditions = []) {
        // Tính toán offset (vị trí bắt đầu)
        $offset = ($page - 1) * $limit;
        
        // Lấy data của trang hiện tại
        $data = $this->find($conditions, $limit, $offset);
        
        // Đếm tổng số bản ghi
        $total = $this->count($conditions);

        // Trả về một mảng kết quả hoàn chỉnh
        return [
            'data' => $data,          // Dữ liệu của trang này
            'total' => $total,        // Tổng số bản ghi
            'page' => $page,          // Trang hiện tại
            'pages' => ceil($total / $limit) // Tổng số trang
        ];
    }
}
