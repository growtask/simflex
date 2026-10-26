**USING clause**

```php
User::findAdv()->leftJoin('user_role', 'role_id');
```
will generate SQL
```sql
SELECT * FROM `user` LEFT JOIN `user_role` USING(`role_id`)
```
**ON clause**

```php
User::findAdv()->leftJoin(UserRole::class, 'user.role_id', 'user_role.role_id');
```
Also, you can omit table names, Active Query will add it automatically
```php
User::findAdv()->leftJoin(UserRole::class, '.role_id', '.role_id');
```
```sql
SELECT * FROM `user` LEFT JOIN `user_role` 
ON `user`.`role_id` = `user_role`.`role_id`
```

**Extra ON clause conditions**

```php
User::findAdv()->leftJoin(UserRole::class, '.role_id', '.role_id', ['privilege' => 'admin']);
```
```sql
SELECT * FROM `user` LEFT JOIN `user_role` 
ON `user`.`role_id` = `user_role`.`role_id` AND `privilege` = :id_...
```
The values are bound as in where()
**Other JOIN types**
```php
User::findAdv()->join('user_role', 'role_id');
```
```sql
SELECT * FROM `user` INNER JOIN `user_role` USING(`role_id`)
```
FULL JOIN
```php
User::findAdv()->join('user_role', 'role_id', null, 'FULL');
```
will generate SQL
```sql
SELECT * FROM `user` FULL JOIN `user_role` USING(`role_id`)
```

**Alias of the joined table**

To join one table several times give it an alias; the omitted table name of the second column is the alias then
```php
Content::findAdv()->leftJoin('content', 'content.pid', '.content_id', alias: 'parent');
```
```sql
SELECT * FROM `content` LEFT JOIN `content` `parent`
ON `content`.`pid` = `parent`.`content_id`
```
