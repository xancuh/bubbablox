<script lang="ts">
	import { link } from "svelte-routing";
	import Main from "../components/templates/Main.svelte";
	import request from "../lib/request";
	import * as rank from "../stores/rank";
	
	let ipdata: {
		[hashed: string]: {
			users: {
				user_id: number;
				username: string;
				hashed_ip: string;
				last_seen: string;
			}[];
		};
	};
	let loading = false;

	$: if (rank.hasPermission("GetUsersList")) {
		loading = true;
		request.get("/users/alts")
			.then((res) => {
				ipdata = res.data?.data;
			})
			.finally(() => {
				loading = false;
			});
	}
</script>

<svelte:head>
	<title>Possible Alts</title>
</svelte:head>

{#await rank.promise then _}
	<Main>
		{#if !rank.hasPermission("GetUsersList")}
			<div class="alert alert-danger">
				You don't have permission to view this page.
			</div>
		{:else}
			<div class="row">
				<div class="col-12">
					<h1>Possible Alts</h1>
					
					{#if loading}
						<div class="text-center my-4">
							<div class="spinner-border" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</div>
					{:else if ipdata}
						<div class="table-responsive">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Hashed IP</th>
										<th>User 1</th>
										<th>User 2</th>
										<th>Last Seen</th>
									</tr>
								</thead>
								<tbody>
									{#each Object.values(ipdata) as data}
										{#if data.users && data.users.length > 0}
											<tr>
												<td>{data.users[0].hashed_ip.slice(0, 10)}...</td>
												<td>
													{#if data.users[0]}
														<a use:link href={`/admin/manage-user/${data.users[0].user_id}`}>
															{data.users[0].username} (ID: {data.users[0].user_id})
														</a>
													{/if}
												</td>
												<td>
													{#if data.users[1]}
														<a use:link href={`/admin/manage-user/${data.users[1].user_id}`}>
															{data.users[1].username} (ID: {data.users[1].user_id})
														</a>
													{/if}
												</td>
												<td>
													{new Date(data.users[0].last_seen).toLocaleString()}
												</td>
											</tr>
										{/if}
									{/each}
								</tbody>
							</table>
						</div>
					{:else}
						<div class="alert alert-info">
							Nothing found.
						</div>
					{/if}
				</div>
			</div>
		{/if}
	</Main>
{/await}