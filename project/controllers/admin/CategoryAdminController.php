<?php
require_once __DIR__ . '/BaseAdminController.php';
require_once __DIR__ . '/../../models/admin/AdminCategoryModel.php';

final class CategoryAdminController extends BaseAdminController
{

    public function index()
    {
        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'status'  => $_GET['status'] ?? '',
            'deleted' => $_GET['deleted'] ?? 0, // 0=active, 1=deleted
        ];

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        $data = AdminCategoryModel::paginate($filters, $page, 10);

        return $this->renderAdmin('admin/catalog/categories', [
            'pagination'=> $data,
            'items'     => $data['items'],
            'total'     => $data['total'],
            'page'      => $page,
            'last_page' => $data['last_page'],
            'filters'   => $filters,
            'allCategories' => AdminCategoryModel::getLevel1(),
            'csrf'      => $this->csrfToken(),
        ]);
    }


    public function create()
    {
        $parents = AdminCategoryModel::getLevel1();

        return $this->renderAdmin('admin/catalog/categories/create', [
            'parents' => $parents
        ]);
    }

    public function store()
    {
        $name = trim($_POST['name']);
        $parentId = $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;
        $active = isset($_POST['is_active']);

        if ($name === '') {
            $this->flashError("Tên danh mục không được để trống.");
            return $this->redirect("index.php?c=categories&a=create");
        }

        if ($parentId !== null) {
            $parent = AdminCategoryModel::find($parentId);
            if (!$parent || $parent['parent_id'] !== null) {
                $this->flashError("Danh mục cha phải là cấp 1.");
                return $this->redirect("index.php?c=categories&a=create");
            }
        }

       
        $slug = $this->slugify($name);
        $original = $slug;
        $i = 1;
        while (AdminCategoryModel::isSlugExist($slug)) {
            $slug = $original . '-' . $i++;
        }

        AdminCategoryModel::create($name, $slug, $parentId, $active);

        $this->flashSuccess("Tạo danh mục thành công.");
        return $this->redirect("index.php?c=categories&a=index");
    }


    public function edit()
    {
        $id = (int)$_GET['id'];
        $category = AdminCategoryModel::find($id);

        if (!$category) {
            $this->flashError("Danh mục không tồn tại.");
            return $this->redirect("index.php?c=categories&a=index");
        }

        return $this->renderAdmin('admin/catalog/categories/edit', [
            'category' => $category,
            'parents'  => AdminCategoryModel::getLevel1(),
        ]);
    }

    public function update()
    {
        $id = (int)$_POST['id'];
        $category = AdminCategoryModel::find($id);

        if (!$category) {
            $this->flashError("Danh mục không tồn tại.");
            return $this->redirect("index.php?c=categories&a=index");
        }

        $name = trim($_POST['name']);
        $parentId = $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;

        if ($parentId === $id) {
            $this->flashError("Không thể chọn chính danh mục làm cha.");
            return $this->redirect("index.php?c=categories&a=edit&id={$id}");
        }

        if ($parentId !== null) {
            $parent = AdminCategoryModel::find($parentId);
            if (!$parent || $parent['parent_id'] !== null) {
                $this->flashError("Danh mục cha phải là cấp 1.");
                return $this->redirect("index.php?c=categories&a=edit&id={$id}");
            }
        }

        $slug = $this->slugify($name);
        $original = $slug;
        $i = 1;
        while (AdminCategoryModel::isSlugExist($slug, $id)) {
            $slug = $original . '-' . $i++;
        }

        AdminCategoryModel::update(
            $id, $name, $slug, $parentId,
            isset($_POST['is_active'])
        );

        $this->flashSuccess("Cập nhật thành công.");
        return $this->redirect("index.php?c=categories&a=edit&id={$id}");
    }

    public function delete()
    {
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? 0);

        $category = AdminCategoryModel::find($id);
        if (!$category) {
            $this->flashError("Danh mục không tồn tại.");
            return $this->redirect("index.php?c=categories&a=index");
        }

        if (AdminCategoryModel::hasChildren($id)) {
            $this->flashError("Không thể xóa danh mục cha khi còn danh mục con.");
            return $this->redirect("index.php?c=categories&a=index");
        }

        if (AdminCategoryModel::hasBooks($id)) {
            $this->flashError("Không thể xóa khi còn sách thuộc danh mục này.");
            return $this->redirect("index.php?c=categories&a=index");
        }

        AdminCategoryModel::softDelete($id);

        $this->flashSuccess("Đã xóa danh mục (Soft Delete).");
        return $this->redirect("index.php?c=categories&a=index");
    }

    public function restore()
    {
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? 0);

        $category = AdminCategoryModel::findDeleted($id);
        if (!$category) {
            $this->flashError("Danh mục không tồn tại hoặc không bị xóa.");
            return $this->redirect("index.php?c=categories&a=index&deleted=1");
        }

        AdminCategoryModel::restore($id);

        $this->flashSuccess("Khôi phục danh mục thành công.");
        return $this->redirect("index.php?c=categories&a=index");
    }
}
