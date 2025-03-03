<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ABZ test task</title>
        <script src="/js/app.js" defer></script>
    </head>
    <body>
        <div class="errors"></div>
        <form method="post" class="form">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" minlength="2" maxlength="60">
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
            </div>
            <div>
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" pattern="\+380\d{9}">
            </div>
            <div>
                <label for="position_id">Position:</label>
                <select id="position_id" name="position_id">
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
            <div>
                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo" accept="image/jpeg, image/jpg">
            </div>
            <button type="submit">Submit</button>
    </form>
    <table border=1>
        <thead>
            <tr>
                <th>id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Position</th>
                <th>Photo</th>
            </tr>
        </thead>
        <tbody id="users_table_body">
            <!-- Users will be populated dynamically -->
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">
                    <button id="load_more" type="button">Load More</button>
                </td>
            </tr>
        </tfoot>
    </table>
    </body>
</html>
