@component('mail::message')

Here are your results, {{ $user->name }}

@if( isset( $changes[ 'students' ][ 'added' ] ) && count( $changes[ 'students' ][ 'added' ] ) )
### **{{ count( $changes[ 'students' ][ 'added' ] ) }} Students Added: **
@component('mail::table')
| ID            | First Name      | Last Name       | Grade | School |
| ------------- |:-------------:|:----------------:|:-----:|:---:
@foreach( $changes[ 'students' ][ 'added' ] as $s )
| {{ $s[ 'student_id' ] }} | {{ $s[ 'first_name' ] }} | {{ $s[ 'last_name' ] }} | {{ $s[ 'grade' ] }} | {{ $campuses[$s[ 'campus_id' ] ]->abbreviation }}
@endforeach
@endcomponent
@endif

@if( isset( $changes[ 'students' ][ 'removed' ] ) && count( $changes[ 'students' ][ 'removed' ] ) )
### **{{ count( $changes[ 'students' ][ 'removed' ] ) }} Students Removed: **
@component('mail::table')
| ID            | First Name      | Last Name       | Grade | School |
| ------------- |:-------------:|:----------------:|:-----:|:---:
@foreach( $changes[ 'students' ][ 'removed' ] as $s )
| {{ $s[ 'student_id' ] }} | {{ $s[ 'first_name' ] }} | {{ $s[ 'last_name' ] }} | {{ $s[ 'grade' ] }} | {{ $campuses[$s[ 'campus_id' ] ]->abbreviation }}
@endforeach
@endcomponent
@endif

@if( isset( $changes[ 'teachers' ][ 'added' ] ) && count( $changes[ 'teachers' ][ 'added' ] ) )
### **{{ count( $changes[ 'teachers' ][ 'added' ] ) }} Teachers Added: **
@component('mail::table')
| ID            | First Name      | Last Name      | School |
| ------------- |:-------------:|:----------------:|:---:
@foreach( $changes[ 'teachers' ][ 'added' ] as $t )
| {{ $t[ 'staff_id' ] }} | {{ $t[ 'first_name' ] }} | {{ $t[ 'last_name' ] }} | {{ $campuses[$t[ 'campus_id' ] ]->abbreviation}}
@endforeach
@endcomponent
@endif

@if( isset( $changes[ 'teachers' ][ 'removed' ] ) && count( $changes[ 'teachers' ][ 'removed' ] ) )
### **{{ count( $changes[ 'teachers' ][ 'removed' ] ) }} Teachers removed: **
@component('mail::table')
| ID            | First Name      | Last Name      | School |
| ------------- |:-------------:|:----------------:|:---:
@foreach( $changes[ 'teachers' ][ 'removed' ] as $t )
| {{ $t[ 'staff_id' ] }} | {{ $t[ 'first_name' ] }} | {{ $t[ 'last_name' ] }} | {{ $campuses[$t[ 'campus_id' ] ]->abbreviation }}
@endforeach
@endcomponent
@endif

{{ config('app.name') }}
@endcomponent