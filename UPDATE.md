### v2.1.0

Добавлены роли для пользователей.

#### Обновление:
- `php artisan migrate`
- Добавить трейт `use ShouldRole;` (use Aweram\UserManagement\Traits\ShouldRole;) в класс пользователя.
- `php artisan um:permissions` - создание прав доступа
