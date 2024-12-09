<?php

namespace Aweram\UserManagement\Livewire;

use App\Models\User;
use Aweram\UserManagement\Facades\PermissionActions;
use Aweram\UserManagement\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Mockery\Exception;

class UserIndexWire extends Component
{
    use WithPagination;

    public string $sortBy = "name";
    public string $sortDirection = "asc";
    public string $searchName = "";
    public string $searchEmail = "";

    public bool $displayDelete = false;
    public bool $displayData = false;
    public int|null $userId = null;

    public string $name = "";
    public string $email = "";
    public array $roles = [];

    public Collection $rolesList;

    protected function queryString(): array
    {
        return [
            "sortBy",
            "sortDirection",
            "searchName" => ["as" => "name", "except" => ""],
            "searchEmail" => ["as" => "email", "except" => ""]
        ];
    }

    /**
     * Валидация форм.
     *
     * @return array[]
     */
    public function rules(): array
    {
        $uniqueCondition = "unique:users,email";
        if ($this->userId) $uniqueCondition .= ",{$this->userId}";
        return [
            "name" => ["required", "string", "max:50"],
            "email" => ["required", "string", "email", "max:50", $uniqueCondition],
        ];
    }

    /**
     * Имена полей.
     *
     * @return string[]
     */
    public function validationAttributes(): array
    {
        return [
            "name" => __("Name"),
            "email" => "E-mail"
        ];
    }

    /**
     * @return View
     */
    public function render(): View
    {
        $query = User::query()->select("id", "name", "email", "super")->with("roles:id,title");
        if (! empty($this->searchName)) {
            $value = trim($this->searchName);
            $query->where("name", "like", "%$value%");
        }
        if (! empty($this->searchEmail)) {
            $value = trim($this->searchEmail);
            $query->where("email", "like", "%$value%");
        }
        $query->orderBy($this->sortBy, $this->sortDirection);

        return view("um::livewire.admin.users", [
            "users" => $query->paginate(),
        ]);
    }

    /**
     * Очистить форму поиска.
     *
     * @return void
     */
    public function clearSearch(): void
    {
        $this->reset("searchEmail", "searchName");
        $this->resetPage();
    }

    /**
     * Изменить сортировку.
     *
     * @param $name
     * @return void
     */
    public function changeSort($name): void
    {
        if ($this->sortBy == $name) {
            $this->sortDirection = $this->sortDirection == "asc" ? "desc" : "asc";
        } else $this->sortDirection = "asc";
        $this->sortBy = $name;
        $this->resetPage();
    }

    /**
     * Показать форму добавления.
     *
     * @return void
     */
    public function showCreate(): void
    {
        $this->resetFields();
        // Проверить авторизацию
        $check = $this->checkAuth("create");
        if (! $check) return;

        $this->displayData = true;
        $this->getRoles();
    }

    /**
     * Добавить пользователя.
     *
     * @return void
     */
    public function store(): void
    {
        // Проверить авторизацию
        $check = $this->checkAuth("create");
        if (! $check) return;
        // Валидация
        $this->validate();
        $newPassword = Str::random(8);
        $user = User::create([
            "name" => $this->name,
            "email" => $this->email,
            "password" => Hash::make($newPassword)
        ]);
        /**
         * @var User $user
         */
        $user->roles()->sync($this->roles);

        session()->flash("success", implode(", ", [
            __("User successfully added"),
            __("Password") . ": $newPassword"
        ]));
        $this->closeData();
        $this->resetPage();
    }

    /**
     * Открыть редактирование пользователя.
     *
     * @param int $userId
     * @return void
     */
    public function showEdit(int $userId): void
    {
        $this->resetFields();
        $this->userId = $userId;
        // Найти пользователя
        $user = $this->findUser();
        if (! $user) return;
        // Проверить авторизацию
        $check = $this->checkAuth("update", $user);
        if (! $check) return;

        $this->name = $user->name;
        $this->email = $user->email;
        $user->load("roles:id");
        foreach ($user->roles as $item) {
            $this->roles[] = $item->id;
        }
        $this->displayData = true;
        $this->getRoles();
    }

    /**
     * Обновление пользователя.
     *
     * @return void
     */
    public function update(): void
    {
        // Найти пользователя
        $user = $this->findUser();
        if (! $user) return;
        // Проверить авторизацию
        $check = $this->checkAuth("update", $user);
        if (! $check) return;
        // Валидация
        $this->validate();
        try {
            $user->update([
                "name" => $this->name,
                "email" => $this->email
            ]);

            $user->roles()->sync($this->roles);
            PermissionActions::forgetRoleIds($user);

            session()->flash("success", __("User successfully updated"));
        } catch (Exception $ex) {
            session()->flash("error", __("Error while update"));
        }

        $this->resetPage();
        $this->closeData();
    }

    /**
     * Закрыть форму редактирования.
     *
     * @return void
     */
    public function closeData(): void
    {
        $this->resetFields();
        $this->displayData = false;
    }

    /**
     * Открыть подтверждение удаления.
     *
     * @param int $userId
     * @return void
     */
    public function showDelete(int $userId): void
    {
        $this->resetFields();
        $this->userId = $userId;
        // Найти пользователя
        $user = $this->findUser();
        if (! $user) return;
        // Проверить авторизацию
        $check = $this->checkAuth("delete", $user);
        if (! $check) return;

        $this->displayDelete = true;
    }

    /**
     * Закрыть форму удаления.
     *
     * @return void
     */
    public function closeDelete(): void
    {
        $this->displayDelete = false;
        $this->resetFields();
    }

    /**
     * Удалить пользователя.
     *
     * @return void
     */
    public function confirmDelete(): void
    {
        // Найти пользователя
        $user = $this->findUser();
        if (! $user) return;
        // Проверить авторизацию
        $check = $this->checkAuth("delete", $user);
        if (! $check) return;

        if ($this->userId !== Auth::id()) {
            try {
                $user->delete();
                session()->flash("success", __("User successfully deleted"));
            } catch (Exception $ex) {
                session()->flash("error", __("User not found"));
            }
        } else {
            session()->flash("error", __("Can't delete yourself"));
        }

        $this->resetPage();
        $this->closeDelete();
    }

    /**
     * Сбросить переменные форм.
     *
     * @return void
     */
    private function resetFields(): void
    {
        $this->reset(["name", "email", "userId", "roles"]);
    }

    /**
     * @return User|null
     */
    private function findUser(): ?User
    {
        $user = User::find($this->userId);
        if (! $user) {
            session()->flash("error", __("User not found"));
            $this->closeData();
            return null;
        }
        return $user;
    }

    private function checkAuth(string $action, User $user = null): bool
    {
        try {
            $this->authorize($action, $user ?? User::class);
            return true;
        } catch (\Exception $ex) {
            session()->flash("error", __("Unauthorized action"));
            $this->closeData();
            $this->closeDelete();
            return false;
        }
    }

    private function getRoles(): void
    {
        $this->rolesList = Role::query()
            ->select("id", "title")
            ->orderBy("title")
            ->get();
    }
}
