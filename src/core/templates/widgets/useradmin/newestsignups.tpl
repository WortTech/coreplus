{script library="jquery"}{/script}
{script location="foot" src="js/user/admin.js"}{/script}

<h3>Recent User Registrations</h3>

<table class="listing">
	<tr>
		<th width="40"></th>
		{if $enableavatar}
			<th>Avatar</th>
		{/if}
		<th sortkey="email">Email</th>
		<th sortkey="active" title="Sort By Active"><abbr title="Active">Active</abbr></th>
		<th sortkey="created">Date Created</th>
		<th>Registration Source</th>
		<th>Registration Invitee</th>
		<th width="100">&nbsp;</th>
	</tr>
	{foreach $users as $user}
		<tr data-userid="{$user.id}" class="user-entry">
			<td>
				{if $user->get('admin')}
					<i class="icon-key" title="Admin Account"></i>
				{/if}
				{foreach $user->getEnabledAuthDrivers() as $auth}
					<i class="icon-{$auth->getAuthIcon()}" title="{$auth->getAuthTitle()}"></i>
				{/foreach}
			</td>

			{if $enableavatar}
				<td>
					{img src="`$user.avatar`" placeholder="person" dimensions="40x40"}
				</td>
			{/if}

			<td>{$user->get('email')}</td>

			<td class="active-status" data-useractive="{$user.active}">
				<noscript>
					{if $user.active == 1}
						<i class="icon-ok" title="Activated"></i>
					{elseif $user.active == -1}
						<i class="icon-times" title="Deactivated"></i>
					{else}
						<i class="icon-exclamation-sign" title="Not Activated Yet"></i>
					{/if}
				</noscript>
			</td>

			<td>{date date="`$user.created`"}</td>
			<td>{$user.registration_source}</td>
			<td>
				{if $user.registration_invitee}
					{user user=$user.registration_invitee}
				{/if}
			</td>

			<td>
				{controls baseurl="/user/view" subject="`$user.id`" hover="true"}
			</td>
		</tr>
	{/foreach}
</table>

<br/>
{a href="/user/admin?sortkey=created&sortdir=down" class="button"}
	<i class="icon-view"></i>
	<span>View All Recent Registrations</span>
{/a}
