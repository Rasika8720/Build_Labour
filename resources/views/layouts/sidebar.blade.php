<nav class="d-none d-md-block sidebar-container">
   <div class="sidebar-header">
      <div class="sidebar-logo">
         <!-- <img class="sidebar-logo-image" src="/img/build-labour-logo-orange.png" 
               srcset="/img/build-labour-logo-orange@2x.png 2x, /img/build-labour-logo-orange@3x.png 3x"/> -->
               <img class="sidebar-logo-image" src="/img/buildlabour-logo.png" />
      </div>
      <div class="sidebar-title">
         {{ config('app.name', 'Appetiser Apps') }}
      </div>
      <div class="sidebar-project">
          ADMIN PORTAL
      </div>
   </div>
   <div class="sidebar-sticky">
      <div class="sidebar-nav-title">
         NAVIGATION
      </div>
      <ul class="sidebar nav flex-column">
         <!-- <li class="nav-item">
            <a class="nav-link nav-link-custom" href="{{ '/all/users' }}">
               Users
            </a>
         </li> -->
         <li class="nav-item">
            <a class="nav-link nav-link-custom" href="{{ '/all/jobs' }}">
               Job Posts
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link nav-link-custom" href="{{ '/all/job-roles' }}">
               Job Roles
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link nav-link-custom" href="{{ '/all/tickets' }}">
               Tickets
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link nav-link-custom" href="{{ '/all/companies' }}">
               Companies
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link nav-link-custom" href="{{ '/all/workers' }}">
               Workers
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link nav-link-custom" href="{{ '/all/exports' }}">
               Export
            </a>
         </li>
         <li class="nav-item">
            <logout text-only="true"></logout>
         </li>
      </ul>
   </div>
</nav>
