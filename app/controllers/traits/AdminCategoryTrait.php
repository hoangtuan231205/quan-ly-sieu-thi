<?php
trait AdminCategoryTrait {
    
    /**
     * ==========================================================================
     * METHOD: categories() - TRANG QUẢN LÝ DANH MỤC (Shell Page)
     * ==========================================================================
     * URL: /admin/categories
     */
    public function categories() {
        $data = [
            'page_title' => 'Quản lý danh mục - Admin',
            'csrf_token' => Session::getCsrfToken()
        ];
        $this->view('admin/categories', $data);
    }

    /**
     * ==========================================================================
     * METHOD: apiCategoriesList() - API LẤY DANH SÁCH DANH MỤC (JSON)
     * ==========================================================================
     * URL: /admin/api/categories  [GET]
     * Params: ?page=1&keyword=
     */
    public function apiCategories() {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12;
        $offset  = ($page - 1) * $perPage;
        $keyword = trim($_GET['keyword'] ?? '');

        $flatCategories = $this->categoryModel->getAllFlat();

        // Filter by keyword
        if ($keyword !== '') {
            $kw = mb_strtolower($keyword);
            $flatCategories = array_values(array_filter($flatCategories, function ($cat) use ($kw) {
                return strpos(mb_strtolower($cat['Ten_danh_muc']), $kw) !== false;
            }));
        }

        // Merge product counts
        $productCounts = $this->categoryModel->getProductCountByCategory();
        $countMap = [];
        foreach ($productCounts as $pc) {
            $countMap[$pc['ID_danh_muc']] = (int)$pc['So_san_pham'];
        }
        foreach ($flatCategories as &$cat) {
            $cat['So_san_pham'] = $countMap[$cat['ID_danh_muc']] ?? 0;
        }
        unset($cat);

        $total      = count($flatCategories);
        $paginated  = array_slice($flatCategories, $offset, $perPage);
        $totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;

        $this->json([
            'success'    => true,
            'data'       => array_values($paginated),
            'pagination' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => $totalPages,
                'from'         => $total > 0 ? $offset + 1 : 0,
                'to'           => min($offset + $perPage, $total),
            ],
        ]);
    }

    /**
     * ==========================================================================
     * METHOD: categoryAdd() - FORM THÊM MỚI
     * ==========================================================================
     * URL: /admin/category-add
     */
    public function categoryAdd() {
        $parents = $this->categoryModel->getForDropdown();
        $data = [
            'page_title'  => 'Thêm danh mục mới - Admin',
            'parents'     => $parents,
            'csrf_token'  => Session::getCsrfToken()
        ];
        $this->view('admin/category_add', $data);
    }

    /**
     * ==========================================================================
     * METHOD: categoryEdit() - FORM SỬA DANH MỤC
     * ==========================================================================
     * URL: /admin/category-edit/{id}
     */
    public function categoryEdit($id = null) {
        if (!$id) redirect(BASE_URL . '/admin/categories');

        $category = $this->categoryModel->findById($id);
        if (!$category) {
            Session::flash('error', 'Danh mục không tồn tại');
            redirect(BASE_URL . '/admin/categories');
            return;
        }

        $parents = $this->categoryModel->getForDropdown();
        $data = [
            'page_title'  => 'Sửa danh mục - Admin',
            'category'    => $category,
            'parents'     => $parents,
            'csrf_token'  => Session::getCsrfToken()
        ];
        $this->view('admin/category_edit', $data);
    }

    /**
     * ==========================================================================
     * METHOD: apiCategorySave() - API LƯU DANH MỤC ADD/EDIT (JSON)
     * ==========================================================================
     * URL: /admin/api/category-save  [POST]
     */
    public function apiCategorySave() {
        if (!$this->isMethod('POST')) {
            $this->json(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!Middleware::verifyCsrf(post('csrf_token'))) {
            $this->json(['success' => false, 'message' => 'Phiên làm việc hết hạn, vui lòng tải lại trang']);
            return;
        }

        $id     = post('id');
        $isEdit = !empty($id);

        $name     = trim(post('ten_danh_muc', ''));
        $parentId = post('danh_muc_cha');
        $parentId = ($parentId === '' || $parentId === '0') ? null : (int)$parentId;
        $order    = (int)post('thu_tu_hien_thi', 0);
        $status   = post('trang_thai', 'active');
        $desc     = trim(post('mo_ta', ''));

        // Validate
        if (empty($name)) {
            $this->json(['success' => false, 'message' => 'Tên danh mục không được để trống']);
            return;
        }

        if ($this->categoryModel->nameExists($name, $isEdit ? $id : null)) {
            $this->json(['success' => false, 'message' => 'Tên danh mục đã tồn tại']);
            return;
        }

        if ($isEdit && $parentId) {
            if (!$this->categoryModel->isValidParent($id, $parentId)) {
                $this->json(['success' => false, 'message' => 'Danh mục cha không hợp lệ (không thể chọn chính mình hoặc con của mình)']);
                return;
            }
        }

        $saveData = [
            'Ten_danh_muc'    => $name,
            'Danh_muc_cha'    => $parentId,
            'Thu_tu_hien_thi' => $order,
            'Trang_thai'      => $status,
            'Mo_ta'           => $desc,
        ];

        if ($isEdit) {
            $result  = $this->categoryModel->update($id, $saveData);
            $message = $result ? 'Cập nhật danh mục thành công' : 'Cập nhật thất bại';
        } else {
            $result  = $this->categoryModel->create($saveData);
            $message = $result ? 'Thêm danh mục thành công' : 'Thêm mới thất bại';
        }

        $this->json([
            'success' => (bool)$result,
            'message' => $message,
        ]);
    }

    /**
     * ==========================================================================
     * METHOD: categoryDelete() - API XÓA DANH MỤC (JSON)
     * ==========================================================================
     * URL: /admin/category-delete  [POST - AJAX]
     */
    public function categoryDelete() {
        if (!$this->isAjax() || !$this->isMethod('POST')) {
            $this->json(['success' => false, 'message' => 'Invalid Request']);
            return;
        }

        if (!Middleware::verifyCsrf(post('csrf_token'))) {
            $this->json(['success' => false, 'message' => 'Phiên làm việc hết hạn']);
            return;
        }

        $id = (int)post('category_id');

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID không hợp lệ']);
            return;
        }

        if ($this->categoryModel->hasChildren($id)) {
            $this->json(['success' => false, 'message' => 'Không thể xóa: Danh mục này có chứa danh mục con']);
            return;
        }

        if ($this->categoryModel->hasProducts($id)) {
            $this->json(['success' => false, 'message' => 'Không thể xóa: Danh mục này đang chứa sản phẩm']);
            return;
        }

        if ($this->categoryModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Xóa danh mục thành công']);
        } else {
            $this->json(['success' => false, 'message' => 'Xóa thất bại']);
        }
    }
}