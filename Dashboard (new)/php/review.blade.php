<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lambda</title>
    @include("$prefix.dashboard-child.layout.stylesheet")
    
</head>

<body>
    <div class="wrapper">
        @include("$prefix.dashboard-child.layout.sidebar")
        <div class="main">
            <div class="border-header-page">
              <div class="container-custom">
                @include("$prefix.dashboard-child.layout.navbar")
            </div>
            </div>
      
            <div class="container-custom">
              <div class="box-task-review">
                <div class="row">
                  <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <h2 class="title-page">Tasks review</h2>
                  </div>
      
                  <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <form>
                      <div class="box-item-filter">
                        <div class="box-input">
                          <div class="search">
                            <input type="text" id="search" name="search" class="form-control" placeholder="คำค้นหา">
                            <i class="fa fa-search"></i>
                          </div>
                        </div>
      
                        <?php
                        $data_filter = [
                          1 => ['name_filter' => 'Practice', "type"=> "pratice"],
                          2 => ['name_filter' => 'Mock test', "type"=> "mocktest"],
                        ];
                        ?>
      
                        <div class="box-button-filter">
                          <div class="row">
                              @foreach($data_filter as $key=>$item)
                              <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6">
                                <a href="{{url("dashboard-child/review?type=".$item['type'])}}" class="btn-filter">
                                  <div class="box-border @if(Request::get('type') == $item['type'])) active-box-filter-review @endif">
                                    <p>{{@$item['name_filter']}}</p>
                                  </div>
                                </a>
                              </div>
                              @endforeach
                          </div>
                        </div>
                      </div>
                    </form>
      
                    <!-- table -->
                    <div class="box-table table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">Title <a href="javascript:void(0)"><i class="lni lni-sort-high-to-low icon-sort-custom"></i></a></th>
                            <th scope="col">Subject</th>
                            <th scope="col">Type</th>
                            <th scope="col" class="text-center">Grade</th>
                            {{-- <th scope="col" class="text-center">Performance <a href="javascript:void(0)"><i class="lni lni-sort-high-to-low icon-sort-custom"></i></a></th> --}}
                            <th scope="col" class="text-center">Date Completed <a href="javascript:void(0)"><i class="lni lni-sort-high-to-low icon-sort-custom"></i></a></th>
                            <th scope="col">Status <a href="javascript:void(0)"><i class="lni lni-sort-high-to-low icon-sort-custom"></i></a></th>
                          </tr>
                        </thead>
                        <tbody>
                          @if (@$pratices)
                            @foreach (@$pratices as $item)
                              @php 
                              $type = "Mock test";
                              if($item->isType == "pratice"){
                                  $type = "Pratice";
                              }

                              $correct_answer = 0;
                              if(@$item->correct_answer != null){
                                  $correct_answer = $item->correct_answer;
                              }

                              $subject_name = "";

                              if(@$item->isType == "pratice")
                              {
                                  if(@$item->subject_id != null){
                                      $subject = \App\Models\Backend\SubjectModel::find($item->subject_id);
                                      $subject_name = $subject->name;
                                  }
                              }elseif(@$item->isType == "mocktest"){
                                  if(@$item->subject_id != null){
                                      $subject = \App\Models\Backend\MocktestModel::find($item->subject_id);
                                      $subject_name = $subject->name;
                                  }
                              }
                              @endphp
                                <tr>
                                    <td>{{@$item->title}}</td>
                                    <td>{{@$subject_name}}</td>
                                    <td>{{@$type}}</td>
                                    <td class="text-center">{{@$item->year->name ?? '-'}}</td>
                                    {{-- <td class="text-center">-</td> --}}
                                    <td class="text-center">{{date('d/m/Y',strtotime($item->complete_date))}}</td>
                                    <td>
                                      @if($item->isActive == "W")
                                          <a class="status-table" href="{{url("$folder/practice/$item->uuid")}}"><span class="text-success">Start Task</span></a>
                                      @elseif($item->isActive == "I")
                                          <a class="status-table" href="{{url("$folder/practice/$item->uuid")}}"><span class="text-success">Continue Task</span></a>
                                      @elseif($item->isActive == "S")
                                          <a class="status-table" href="{{url("$folder/practice-transcript/$item->uuid")}}"><span class="text-success">Check Transcript</span></a>
                                      @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                      </table>

                      <div id="pagination" class="pagination-container"></div>
                    </div>
                    <!-- end table -->
                  </div>
      
      
      
                </div>
              </div>
            </div>
          </div>
    </div>
    @include("$prefix.dashboard-child.layout.javascript")
    <script>
      document.addEventListener("DOMContentLoaded", function () {
          const searchInput = document.getElementById("search");
          const tableRows = document.querySelectorAll(".box-table tbody tr");
    
          searchInput.addEventListener("keyup", function () {
              let searchText = searchInput.value.toLowerCase();
    
              tableRows.forEach(row => {
                  let title = row.cells[0].innerText.toLowerCase(); // คอลัมน์ Title
                  let subject = row.cells[1].innerText.toLowerCase(); // คอลัมน์ Subject
    
                  if (title.includes(searchText) || subject.includes(searchText)) {
                      row.style.display = "";
                  } else {
                      row.style.display = "none";
                  }
              });
          });
      });
    </script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
      const searchInput = document.getElementById("search");
      const tableRows = Array.from(document.querySelectorAll(".box-table tbody tr"));
      const rowsPerPage = 20; // จำนวนแถวต่อหน้า
      let currentPage = 1;

      function displayTableRows() {
          let startIndex = (currentPage - 1) * rowsPerPage;
          let endIndex = startIndex + rowsPerPage;

          tableRows.forEach((row, index) => {
              if (index >= startIndex && index < endIndex) {
                  row.style.display = "";
              } else {
                  row.style.display = "none";
              }
          });

          updatePaginationButtons();
      }

      function updatePaginationButtons() {
          let totalPages = Math.ceil(tableRows.length / rowsPerPage);
          let paginationContainer = document.getElementById("pagination");
          paginationContainer.innerHTML = "";

          for (let i = 1; i <= totalPages; i++) {
              let button = document.createElement("button");
              button.innerText = i;
              button.classList.add("pagination-btn");
              if (i === currentPage) button.classList.add("active");

              button.addEventListener("click", function () {
                  currentPage = i;
                  displayTableRows();
              });

              paginationContainer.appendChild(button);
          }
      }

      searchInput.addEventListener("keyup", function () {
          let searchText = searchInput.value.toLowerCase();
          let filteredRows = tableRows.filter(row => {
              let title = row.cells[0].innerText.toLowerCase();
              let subject = row.cells[1].innerText.toLowerCase();
              return title.includes(searchText) || subject.includes(searchText);
          });

          tableRows.forEach(row => row.style.display = "none");
          filteredRows.forEach(row => row.style.display = "");

          currentPage = 1;
          displayTableRows();
      });

      displayTableRows();
  });
</script>

</body>

</html>
