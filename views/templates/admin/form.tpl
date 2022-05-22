<form method="post">
        <select name="branch">
                {foreach from=$branches item=branch}
        <option value="{$branch}">{$branch}</option>
                {/foreach}
        </select>
        <br>
        <button name="updateBranch" type="submit" value="1">Update</button>
</form>
